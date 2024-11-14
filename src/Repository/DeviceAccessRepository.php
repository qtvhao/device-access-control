<?php
namespace Qtvhao\DeviceAccessControl\Repository;

use Qtvhao\DeviceAccessControl\Core\Data\DeviceData;
use Qtvhao\DeviceAccessControl\Core\Entities\Device;
use Qtvhao\DeviceAccessControl\Core\Interfaces\DeviceAccessRepositoryInterface;
use Qtvhao\DeviceAccessControl\Model\Device as DeviceModel;
use Psr\Log\LoggerInterface;

class DeviceAccessRepository implements DeviceAccessRepositoryInterface
{
    protected DeviceModel $model;
    protected LoggerInterface $logger;

    public function __construct(DeviceModel $model, LoggerInterface $logger)
    {
        $this->model = $model;
        $this->logger = $logger;
    }

    public function countByDeviceType(string $userId, string $deviceType): int
    {
        try {
            return $this->model
                ->where('device_type', $deviceType)
                ->where('user_id', $userId)->count();
        } catch (\Exception $e) {
            $this->logger->error("Failed to count devices by type", [
                'error' => $e->getMessage(),
                'userId' => $userId,
                'deviceType' => $deviceType
            ]);
            return 0; // Trả về 0 nếu có lỗi
        }
    }

    public function updateLastAccessTime($deviceId, $userId, \DateTime $currentTime): bool {
        try {
            $device = $this->model->newQuery()->where('device_id', $deviceId)->where('user_id', $userId)->first();
            if ($device === null) {
                return false;
            }
            $device->last_accessed = $currentTime;
            return $device->save();
        } catch (\Exception $e) {
            $this->logger->error("Failed to update last access time", [
                'error' => $e->getMessage(),
                'deviceId' => $deviceId,
                'userId' => $userId
            ]);
            return false; // Trả về false nếu có lỗi
        }
    }

    public function save(DeviceData $deviceData): Device
    {
        try {
            $saved = $this->model->newQuery()->create([
                'device_uuid' => $deviceData->getDeviceUuid(),
                'device_type' => $deviceData->getDeviceType(),
                'device_name' => $deviceData->getDeviceName(),
                'user_id' => $deviceData->getUserId()
            ]);
            // cast the model to the entity
            return new Device(
                deviceUuid: $saved->device_uuid,
                deviceType: $saved->device_type,
                deviceName: $saved->device_name,
                userId: $saved->user_id
            );
        } catch (\Exception $e) {
            $this->logger->error("Failed to save device data", [
                'error' => $e->getMessage(),
                'deviceData' => $deviceData
            ]);
            throw new \RuntimeException("Unable to save device data", 0, $e);
        }
    }

    public function findByDeviceUuid(string $deviceUuid, string $userId): ?Device
    {
        try {
            /**
             * @var DeviceModel $device
             */
            $device = $this->model->where('device_uuid', $deviceUuid)->where('user_id', $userId)->first();
            if ($device === null) {
                return null;
            }
            return new Device(
                deviceUuid: $device->device_uuid,
                deviceType: $device->device_type,
                deviceName: $device->device_name,
                userId: $device->user_id
            );
        } catch (\Exception $e) {
            $this->logger->error("Failed to find device by deviceUuid", [
                'error' => $e->getMessage(),
                'deviceUuid' => $deviceUuid,
                'userId' => $userId
            ]);
            return null; // Trả về null nếu có lỗi
        }
    }
}
