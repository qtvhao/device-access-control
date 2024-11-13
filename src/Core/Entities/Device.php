<?php
namespace Qtvhao\DeviceAccessControl\Core\Entities;

class Device
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
    )
    {
        $this->deviceId = $deviceId;
        $this->deviceType = $deviceType;
        $this->deviceName = $deviceName;
        $this->userId = $userId;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function setDeviceId($deviceId)
    {
        $this->deviceId = $deviceId;
    }
    
    public function setDeviceType($deviceType)
    {
        $this->deviceType = $deviceType;
    }

    public function getDeviceId()
    {
        return $this->deviceId;
    }

    public function getDeviceType()
    {
        return $this->deviceType;
    }
}