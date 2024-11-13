<?php

namespace Qtvhao\DeviceAccessControl\Core\Interfaces;

use Qtvhao\DeviceAccessControl\Core\Data\DeviceData;
use Qtvhao\DeviceAccessControl\Core\Entities\Device;

interface DeviceAccessRepositoryInterface {
    public function save(DeviceData $device): Device;
    public function findByDeviceId(string $deviceId, string $userId): ?Device;
    public function countByDeviceType(string $userId, string $deviceType): int;
}
