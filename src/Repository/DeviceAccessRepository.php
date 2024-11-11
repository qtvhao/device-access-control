<?php
namespace Qtvhao\DeviceAccessControl\Repository;

use Qtvhao\DeviceAccessControl\Core\Data\DeviceData;
use Qtvhao\DeviceAccessControl\Core\Entities\Device;
use Qtvhao\DeviceAccessControl\Core\Interfaces\DeviceAccessRepositoryInterface;

class DeviceAccessRepository implements DeviceAccessRepositoryInterface
{
    public function save(DeviceData $deviceData): Device
    {
        $device = new Device(
            deviceId: $deviceData->getDeviceId(),
            deviceType: $deviceData->getDeviceType()
        );

        return $device;
    }
}
