<?php
namespace Qtvhao\DeviceAccessControl\Contracts;

interface DeviceStorageInterface
{
    public function getDevice($userId, $deviceType);
    public function storeDevice($userId, $deviceId, $deviceType);
    public function removeDevice($userId, $deviceType);
}
