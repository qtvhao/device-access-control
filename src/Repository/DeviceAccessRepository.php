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

    public function countByDeviceType(string $userId, string $deviceType): int
    {
        return $this->model
            ->where('device_type', $deviceType)
            ->where('user_id', $userId)->count();
    }

    public function updateLastAccessTime($deviceId, $userId, \DateTime $currentTime): bool {
        $device = $this->model->newQuery()->where('device_id', $deviceId)->where('user_id', $userId)->first();
        if ($device === null) {
            return false;
        }
        $device->last_accessed = $currentTime;
        return $device->save();
    }

    public function save(DeviceData $deviceData): Device
    {
        $saved = $this->model->newQuery()->create([
            'device_id' => $deviceData->getDeviceId(),
            'device_type' => $deviceData->getDeviceType(),
            'device_name' => $deviceData->getDeviceName(),
            'user_id' => $deviceData->getUserId()
        ]);
        // cast the model to the entity
        return new Device(
            deviceId: $saved->device_id,
            deviceType: $saved->device_type,
            deviceName: $saved->device_name,
            userId: $saved->user_id
        );
    }

    public function findByDeviceId(string $deviceId, string $deviceType): ?Device
    {
        $device = $this->model->where('device_id', $deviceId)->first();

        if ($device === null) {
            return null;
        }

        return new Device(
            deviceId: $device->device_id,
            deviceType: $device->device_type,
            deviceName: $device->device_name,
            userId: $device->user_id
        );
    }
}
