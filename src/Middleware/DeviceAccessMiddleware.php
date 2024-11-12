<?php
namespace Qtvhao\DeviceAccessControl\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Qtvhao\DeviceAccessControl\Core\UseCases\DeviceAccessOrchestrator;

class DeviceAccessMiddleware
{
    protected $orchestrator;

    public function __construct(DeviceAccessOrchestrator $orchestrator)
    {
        $this->orchestrator = $orchestrator;
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
        // Get the required headers for device validation
        $deviceId = $request->header('Device-ID');
        $deviceType = $request->header('Device-Type');

        if (!$deviceId || !$deviceType) {
            // Return an error if headers are missing
            return new Response('Thiếu thông tin thiết bị', Response::HTTP_BAD_REQUEST);
        }

        // Execute device check
        $userId = $request->user()->id;
        $isAllowed = $this->orchestrator->execute($userId, $deviceId, $deviceType);

        if (!$isAllowed) {
            // If access is denied due to device limit, return an error response
            return new Response('Vượt quá giới hạn thiết bị truy cập', Response::HTTP_FORBIDDEN);
        }

        // Allow request to proceed
        return $next($request);
    }
}