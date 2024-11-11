<?php
namespace Qtvhao\DeviceAccessControl\Core\Entities;

class Device
{
    private $deviceId;
    private $deviceType;

    public function __construct()
    {
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