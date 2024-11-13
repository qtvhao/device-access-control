<?php

namespace Qtvhao\DeviceAccessControl\Tests\Unit;

use PHPUnit\Framework\TestCase;

use Qtvhao\DeviceAccessControl\Core\UseCases\AddNewDeviceUseCase;
use Qtvhao\DeviceAccessControl\Core\Interfaces\DeviceAccessRepositoryInterface;
use Qtvhao\DeviceAccessControl\Core\Data\DeviceData;
use Qtvhao\DeviceAccessControl\Core\Entities\Device;

class AddNewDeviceUseCaseTest extends TestCase
{
    public function test_add_new_device()
    {
        $deviceData = new DeviceData(
            userId: 123,
            deviceId: '123456',
            deviceName: 'Web Device',
            deviceType: 'Web'
        );
        $deviceRepositoryMock = $this->createMock(DeviceAccessRepositoryInterface::class);
        $deviceRepositoryMock->expects($this->once())
                             ->method('save')
                             ->with($deviceData)
                                ->willReturn(new Device(
                                    deviceId: $deviceData->getDeviceId(),
                                    deviceType: $deviceData->getDeviceType(),
                                    deviceName: $deviceData->getDeviceName(),
                                    userId: $deviceData->getUserId()
                                ));

        $useCase = new AddNewDeviceUseCase($deviceRepositoryMock);
        $saved = $useCase->execute($deviceData);
        $this->assertEquals($saved->getDeviceType(), 'Web');
        $this->assertEquals($saved->getDeviceId(), '123456');
    }
}
