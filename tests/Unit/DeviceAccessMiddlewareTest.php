<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Qtvhao\DeviceAccessControl\Core\UseCases\DeviceAccessOrchestrator;
use Qtvhao\DeviceAccessControl\Middleware\DeviceAccessMiddleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery;

class DeviceAccessMiddlewareTest extends TestCase
{
    public function test_device_access_allowed()
    {
        $orchestratorMock = Mockery::mock(DeviceAccessOrchestrator::class);
        $orchestratorMock->shouldReceive('execute')
                         ->andReturn(true);

        $middleware = new DeviceAccessMiddleware($orchestratorMock);
        $request = Request::create('/learning', 'GET', [], [], [], ['HTTP_Device-ID' => 'abc123', 'HTTP_Device-Type' => 'Web'])
            ->setUserResolver(function () {
                return (object) ['id' => 1];
            });
        
        $response = $middleware->handle($request, function () {
            return new Response('Access Granted');
        });

        $this->assertEquals('Access Granted', $response->getContent());
    }

    public function test_device_access_denied()
    {
        $orchestratorMock = Mockery::mock(DeviceAccessOrchestrator::class);
        $orchestratorMock->shouldReceive('execute')
                         ->andReturn(false);

        $middleware = new DeviceAccessMiddleware($orchestratorMock);
        $request = Request::create('/learning', 'GET', [], [], [], ['HTTP_Device-ID' => 'abc123', 'HTTP_Device-Type' => 'Web'])
            ->setUserResolver(function () {
                return (object) ['id' => 1];
            });
        
        $response = $middleware->handle($request, function () {
            return new Response('Access Granted');
        });

        $this->assertEquals(403, $response->getStatusCode());
    }
}
