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
        $deviceData = new DeviceData([
            'deviceId' => '123456',
            'deviceType' => 'Web'
        ]);
        $device = new Device(
            deviceId: $deviceData->getDeviceId(),
            deviceType: $deviceData->getDeviceType()
        );
        
        $deviceRepositoryMock = $this->createMock(DeviceAccessRepositoryInterface::class);
        $deviceRepositoryMock->expects($this->once())
                             ->method('save')
                             ->with($device);

        $useCase = new AddNewDeviceUseCase($deviceRepositoryMock);
        $saved = $useCase->execute($deviceData);
        $this->assertEquals($device, $saved);
    }
}
