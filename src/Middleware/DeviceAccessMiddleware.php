<?php
namespace Qtvhao\DeviceAccessControl\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Qtvhao\DeviceAccessControl\Core\UseCases\DeviceAccessOrchestrator;
use Qtvhao\DeviceAccessControl\Core\Data\DeviceData;
use \Tymon\JWTAuth\JWT;
use \Tymon\JWTAuth\Contracts\Providers\Auth;

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
        if (!$request->bearerToken()) {
            return new Response('The token could not be parsed from the request', Response::HTTP_BAD_REQUEST);
        }
        // Try to get the authenticated user from the token
        $jwtToken = new \Tymon\JWTAuth\Token($request->bearerToken());
        $jwtPayload = $this->jwt->decode($jwtToken);
        if (!$user = $this->auth->byId(
            $jwtPayload->getSubject())
        ) {
            return new Response('Không có quyền truy cập', Response::HTTP_UNAUTHORIZED);
        }
        $deviceInfo = $jwtPayload->toArray()['dev'] ?? null;
        if (!$deviceInfo || !is_array($deviceInfo)) {
            return new Response('Thiếu thông tin thiết bị', Response::HTTP_BAD_REQUEST);
        }
        // Get the device ID from the JWT payload or request headers
        $deviceId = $deviceInfo['id'];
        $deviceType = $deviceInfo['type'];
        $deviceName = $request->header('Device-Name', $request->header('User-Agent', 'Unknown'));

        if (!$deviceId || !$deviceType) {
            // Return an error if headers are missing
            return new Response('Thiếu thông tin thiết bị', Response::HTTP_BAD_REQUEST);
        }

        // Execute device check
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