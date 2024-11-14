<?php
namespace Qtvhao\DeviceAccessControl\Core\Entities;

class Device
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
    )
    {
        $this->deviceUuid = $deviceUuid;
        $this->deviceType = $deviceType;
        $this->deviceName = $deviceName;
        $this->userId = $userId;
    }

    public function getDeviceName()
    {
        return $this->deviceName;
    }

    public function setDeviceName($deviceName)
    {
        $this->deviceName = $deviceName;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function setDeviceUuid($deviceUuid)
    {
        $this->deviceUuid = $deviceUuid;
    }
    
    public function setDeviceType($deviceType)
    {
        $this->deviceType = $deviceType;
    }

    public function getDeviceUuid()
    {
        return $this->deviceUuid;
    }

    public function getDeviceType()
    {
        return $this->deviceType;
    }
}