<?php
namespace Qtvhao\DeviceAccessControl\Repository;

use Qtvhao\DeviceAccessControl\Core\Data\DeviceData;
use Qtvhao\DeviceAccessControl\Core\Entities\Device;
use Qtvhao\DeviceAccessControl\Core\Interfaces\DeviceAccessRepositoryInterface;
use Qtvhao\DeviceAccessControl\Model\Device as DeviceModel;

class DeviceAccessRepository implements DeviceAccessRepositoryInterface
{
    protected DeviceModel $model;

    public function __construct(DeviceModel $model)
    {
        $this->model = $model;
    }

    public function save(DeviceData $deviceData): Device
    {
        $device = new Device(
            deviceId: $deviceData->getDeviceId(),
            deviceType: $deviceData->getDeviceType()
        );

        $saved = $this->model->create([
            'device_id' => $device->getDeviceId(),
            'device_type' => $device->getDeviceType()
        ]);

        return new Device(
            deviceId: $saved->device_id,
            deviceType: $saved->device_type
        );
    }

    public function findByDeviceId(string $deviceId): ?Device
    {
        $device = $this->model->where('device_id', $deviceId)->first();

        if ($device === null) {
            return null;
        }

        return new Device(
            deviceId: $device->device_id,
            deviceType: $device->device_type
        );
    }
}
