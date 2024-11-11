<?php

namespace Qtvhao\DeviceAccessControl\Core\Interfaces;

use Qtvhao\DeviceAccessControl\Core\Data\DeviceData;
use Qtvhao\DeviceAccessControl\Core\Entities\Device;

interface DeviceAccessRepositoryInterface {
    public function save(DeviceData $device): Device;
}