<?php
namespace Qtvhao\DeviceAccessControl\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Qtvhao\DeviceAccessControl\Core\UseCases\DeviceAccessOrchestrator;
use Qtvhao\DeviceAccessControl\Core\Data\DeviceData;
use Qtvhao\DeviceAccessControl\Core\Enums\DeviceEnums;
use \Tymon\JWTAuth\JWT;
use \Tymon\JWTAuth\Contracts\Providers\Auth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class DeviceAccessMiddleware
{
    protected $orchestrator;
    protected $auth;
    protected $jwt;

    public function __construct(
        DeviceAccessOrchestrator $orchestrator,
        JWT $jwt,
        Auth $auth
    )
    {
        $this->orchestrator = $orchestrator;
        $this->jwt = $jwt;
        $this->auth = $auth;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$token = $request->bearerToken()) {
            return new Response('Token không tồn tại trong request', Response::HTTP_BAD_REQUEST);
        }
    

        try {
            // Giải mã token và lấy payload
            $jwtToken = new \Tymon\JWTAuth\Token($token);
            $jwtPayload = $this->jwt->decode($jwtToken);
        } catch (TokenExpiredException $e) {
            // Try refreshing the token
            try {
                $newToken = $this->jwt->refresh($jwtToken);
                // Attach the new token to the response headers for the client
                $request->headers->set('Authorization', 'Bearer ' . $newToken);
                $jwtPayload = $this->jwt->decode(new \Tymon\JWTAuth\Token($newToken));
            } catch (JWTException $refreshException) {
                return new Response('Token đã hết hạn và không thể làm mới', Response::HTTP_UNAUTHORIZED);
            }
        } catch (TokenInvalidException $e) {
            return new Response('Token không hợp lệ', Response::HTTP_BAD_REQUEST);
        } catch (JWTException $e) {
            return new Response('Token không hợp lệ', Response::HTTP_BAD_REQUEST);
        }
    
        $user = $this->auth->byId($jwtPayload->getSubject());
    
        if (!$user) {
            return new Response('Không có quyền truy cập', Response::HTTP_UNAUTHORIZED);
        }
        $deviceInfo = $jwtPayload->toArray()['dev'] ?? null;
        if (!$deviceInfo || !is_array($deviceInfo)) {
            return new Response('Thiếu thông tin thiết bị', Response::HTTP_BAD_REQUEST);
        }
    
        $deviceId = $deviceInfo['id'] ?? null;
        $deviceType = $deviceInfo['type'] ?? null;
    
        if (!$deviceId || !$deviceType) {
            return new Response('Thiếu Device ID hoặc Device Type', Response::HTTP_BAD_REQUEST);
        }
        if (!in_array($deviceType, [
            DeviceEnums::DEVICE_TYPE_WEB_BROWSER,
            DeviceEnums::DEVICE_TYPE_MOBILE,
            DeviceEnums::DEVICE_TYPE_TABLET
        ])) {
            return new Response('Loại thiết bị không hợp lệ', Response::HTTP_BAD_REQUEST);
        }
    
        $deviceName = $request->header('Device-Name', $request->header('User-Agent', 'Unknown'));
        $userId = $user->id;
        $isAllowed = $this->orchestrator->execute(new DeviceData(
            deviceId: $deviceId,
            deviceType: $deviceType,
            deviceName: $deviceName,
            userId: $userId
        ));

        if (!$isAllowed) {
            // If access is denied due to device limit, return an error response
            return new Response('Vượt quá giới hạn thiết bị truy cập', Response::HTTP_FORBIDDEN);
        }

        // Allow request to proceed
        return $next($request);
    }
}