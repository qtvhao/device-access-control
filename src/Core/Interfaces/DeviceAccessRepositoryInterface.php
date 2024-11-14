<?php

namespace Qtvhao\DeviceAccessControl\Core\Interfaces;

use Qtvhao\DeviceAccessControl\Core\Data\DeviceData;
use Qtvhao\DeviceAccessControl\Core\Entities\Device;

interface DeviceAccessRepositoryInterface {
    public function updateLastAccessTime(string $deviceId, string $userId, \DateTime $lastAccessTime): bool;
    public function save(DeviceData $device): Device;
    public function findByDeviceUuid(string $deviceUuid, string $userId): ?Device;
    public function countByDeviceType(string $userId, string $deviceType): int;
}
