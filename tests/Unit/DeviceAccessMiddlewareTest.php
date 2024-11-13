<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Qtvhao\DeviceAccessControl\Core\UseCases\DeviceAccessOrchestrator;
use Qtvhao\DeviceAccessControl\Middleware\DeviceAccessMiddleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery;
use Qtvhao\DeviceAccessControl\Core\Enums\DeviceEnums;

class DeviceAccessMiddlewareTest extends TestCase
{
    private $userId = 1;
    public function test_device_access_allowed()
    {
        $orchestratorMock = Mockery::mock(DeviceAccessOrchestrator::class);
        $orchestratorMock->shouldReceive('execute')
            ->andReturn(true);

        $jwtAuthMock = Mockery::mock('alias:Tymon\JWTAuth\Facades\JWTAuth');
        $jwtAuthMock->shouldReceive('parseToken')->andReturnSelf();
        $jwtAuthMock->shouldReceive('authenticate')->andReturn((object) ['id' => $this->userId]);
        $jwtAuthMock->shouldReceive('getPayload')->andReturn((object) [
            'dev' => [
                'id' => 'abc123',
                'type' => DeviceEnums::DEVICE_TYPE_WEB
            ]
        ]);
         

        $middleware = new DeviceAccessMiddleware($orchestratorMock, $jwtAuthMock);
        $request = Request::create('/learning', 'GET', [], [], [], [
            'HTTP_Authorization' => 'Bearer some-valid-token'
        ]);
        
        $response = $middleware->handle($request, function () {
            return new Response('Access Granted');
        });

        $this->assertEquals('Access Granted', $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_device_access_denied()
    {
        $orchestratorMock = Mockery::mock(DeviceAccessOrchestrator::class);
        $orchestratorMock->shouldReceive('execute')
                         ->andReturn(false);

        $jwtAuthMock = Mockery::mock('alias:Tymon\JWTAuth\Facades\JWTAuth');
        $jwtAuthMock->shouldReceive('parseToken')->andReturnSelf();
        $jwtAuthMock->shouldReceive('authenticate')->andReturn((object) ['id' => $this->userId]);
        $jwtAuthMock->shouldReceive('getPayload')->andReturn((object) [
            'dev' => [
                'id' => 'abc123',
                'type' => DeviceEnums::DEVICE_TYPE_WEB
            ]
        ]);
        $middleware = new DeviceAccessMiddleware($orchestratorMock, $jwtAuthMock);
        $request = Request::create('/learning', 'GET', [], [], [], [
            'HTTP_Authorization' => 'Bearer some-valid-token'
        ]);
        
        $response = $middleware->handle($request, function () {
            return new Response('Access Granted');
        });

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals('Vượt quá giới hạn thiết bị truy cập', $response->getContent());
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}
