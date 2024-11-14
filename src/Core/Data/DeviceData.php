<?php
namespace Qtvhao\DeviceAccessControl\Core\Data;

class DeviceData
{
    private $deviceUuid;
    private $deviceType;
    private $deviceName;
    private $userId;

    public function __construct(
        string $deviceUuid,
        string $deviceType,
        string $deviceName,
        int $userId
    ) {
        $this->deviceUuid = $deviceUuid;
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

    public function getDeviceUuid(): string
    {
        return $this->deviceUuid;
    }

    public function setDeviceUuid(string $deviceId): void
    {
        $this->deviceUuid = $deviceId;
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
