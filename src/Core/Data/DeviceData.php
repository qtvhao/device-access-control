<?php
namespace Qtvhao\DeviceAccessControl\Core\Data;

class DeviceData
{
    private $deviceId;
    private $deviceType;

    public function __construct(array $data)
    {
        $this->deviceId = $data['deviceId'];
        $this->deviceType = $data['deviceType'];
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
}
