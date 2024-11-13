<?php
namespace Qtvhao\DeviceAccessControl\Core\Data;

class DeviceData
{
    private $deviceId;
    private $deviceType;
    private $deviceName;
    private $userId;

    public function __construct(
        string $deviceId,
        string $deviceType,
        string $deviceName,
        int $userId
    ) {
        $this->deviceId = $deviceId;
        $this->deviceType = $deviceType;
        $this->deviceName = $deviceName;
        $this->userId = $userId;
    }

    public function getDeviceName(): string
    {
        return $this->deviceName;
    }

    public function setDeviceName(string $deviceName): void
    {
        $this->deviceName = $deviceName;
    }

    public function getDeviceId(): string
    {
        return $this->deviceId;
    }

    public function setDeviceId(string $deviceId): void
    {
        $this->deviceId = $deviceId;
    }

    public function getDeviceType(): string
    {
        return $this->deviceType;
    }

    public function setDeviceType(string $deviceType): void
    {
        $this->deviceType = $deviceType;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }
}
