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
            deviceType: 'Web'
        );
        $deviceRepositoryMock = $this->createMock(DeviceAccessRepositoryInterface::class);
        $deviceRepositoryMock->expects($this->once())
                             ->method('save')
                             ->with($deviceData)
                                ->willReturn(new Device(
                                    deviceId: $deviceData->getDeviceId(),
                                    deviceType: $deviceData->getDeviceType()
                                ));

        $useCase = new AddNewDeviceUseCase($deviceRepositoryMock);
        $saved = $useCase->execute($deviceData);
        $this->assertEquals($saved->getDeviceType(), 'Web');
        $this->assertEquals($saved->getDeviceId(), '123456');
    }
}
