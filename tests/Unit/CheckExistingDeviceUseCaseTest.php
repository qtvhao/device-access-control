<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Qtvhao\DeviceAccessControl\Core\UseCases\CheckExistingDeviceUseCase;
use Qtvhao\DeviceAccessControl\Core\Interfaces\DeviceAccessRepositoryInterface;
use Qtvhao\DeviceAccessControl\Core\Entities\Device;

use Mockery;

class CheckExistingDeviceUseCaseTest extends TestCase
{

    public function test_allow_access_when_device_already_saved()
    {
        $deviceRepositoryMock = Mockery::mock(DeviceAccessRepositoryInterface::class);
        
        $existingDevice = new Device(
            deviceId: 'abc123',
            deviceType: 'Web',
            deviceName: 'Web Device',
            userId: 1
        );

        // Assume that the device with ID 'abc123' is already saved
        $deviceRepositoryMock->shouldReceive('findByDeviceId')
                             ->with($existingDevice->getDeviceId(), $existingDevice->getUserId())
                             ->andReturn($existingDevice); // Giả lập thiết bị được trả về từ repository

        // Construct the use case with the mocked repository
        $useCase = new CheckExistingDeviceUseCase($deviceRepositoryMock);
        
        $result = $useCase->execute($existingDevice->getDeviceId(), $existingDevice->getUserId());

        // If the device with ID 'abc123' is already saved before, access should be allowed
        $this->assertTrue($result, "Access should be allowed when device already saved before");
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}