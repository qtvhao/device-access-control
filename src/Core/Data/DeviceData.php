<?php
namespace Qtvhao\DeviceAccessControl\Core\Data;

class DeviceData
{
    private $deviceId;
    private $deviceType;
    private $userId;

    public function __construct(
        string $deviceId,
        string $deviceType,
        int $userId
    ) {
        $this->deviceId = $deviceId;
        $this->deviceType = $deviceType;
        $this->userId = $userId;
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
