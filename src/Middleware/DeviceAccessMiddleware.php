<?php
namespace Qtvhao\DeviceAccessControl\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Qtvhao\DeviceAccessControl\Core\UseCases\DeviceAccessOrchestrator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\JWT;
use Qtvhao\DeviceAccessControl\Core\Data\DeviceData;

class DeviceAccessMiddleware
{
    protected $orchestrator;
    /**
     * @var JWTAuth|JWT
     */
    protected $jwtAuth;

    public function __construct(DeviceAccessOrchestrator $orchestrator, JWTAuth $jwtAuth)
    {
        $this->orchestrator = $orchestrator;
        $this->jwtAuth = $jwtAuth;
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
        if (!$user = $this->jwtAuth->parseToken()->authenticate()) {
            return new Response('Không có quyền truy cập', Response::HTTP_UNAUTHORIZED);
        }

        $jwtPayload = $this->jwtAuth->getPayload();
        $deviceInfo = $jwtPayload?->dev ?? null;
        if (!$deviceInfo || !is_array($deviceInfo)) {
            return new Response('Thiếu thông tin thiết bị', Response::HTTP_BAD_REQUEST);
        }
        // Get the device ID from the JWT payload or request headers
        $deviceId = $deviceInfo['id'];
        $deviceType = $deviceInfo['type'];

        if (!$deviceId || !$deviceType) {
            // Return an error if headers are missing
            return new Response('Thiếu thông tin thiết bị', Response::HTTP_BAD_REQUEST);
        }

        // Execute device check
        $userId = $user->id;
        $isAllowed = $this->orchestrator->execute(new DeviceData(
            deviceId: $deviceId,
            deviceType: $deviceType,
            deviceName: $request->header('User-Agent'),
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