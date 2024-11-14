<?php
namespace Qtvhao\DeviceAccessControl\Events;

class DeviceAccessUpdatedEvent
{
    public $deviceId;
    public $userId;

    public function __construct(string $deviceId, string $userId)
    {
        $this->deviceId = $deviceId;
        $this->userId = $userId;
    }
}
