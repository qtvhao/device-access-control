<?php
namespace Qtvhao\DeviceAccessControl\Cache;

use Qtvhao\DeviceAccessControl\Core\Data\DeviceData;
use Qtvhao\DeviceAccessControl\Core\Entities\Device;
use Qtvhao\DeviceAccessControl\Core\Interfaces\DeviceAccessRepositoryInterface;
use Redis;

class DeviceAccessCacheDecorator implements DeviceAccessRepositoryInterface
{
    private DeviceAccessRepositoryInterface $repository;
    private Redis $cache;

    public function __construct(DeviceAccessRepositoryInterface $repository, Redis $cache)
    {
        $this->repository = $repository;
        $this->cache = $cache;
    }

    public function save(DeviceData $device): Device
    {
        $device = $this->repository->save($device);

        $this->cache->set("device:{$device->getDeviceId()}", json_encode([
            'deviceId' => $device->getDeviceId(),
            'deviceType' => $device->getDeviceType()
        ]));

        return $device;
    }

    public function findByDeviceId(string $deviceId): ?Device
    {
        $cachedData = $this->cache->get("device:$deviceId");
        if ($cachedData) {
            $device = json_decode($cachedData, true);
            return new Device(
                deviceId: $device['deviceId'],
                deviceType: $device['deviceType']
            );
        }

        $device = $this->repository->findByDeviceId($deviceId);
        $this->cache->set("device:$deviceId", json_encode([
            'deviceId' => $device->getDeviceId(),
            'deviceType' => $device->getDeviceType()
        ]));

        return $device;
    }

    public function countByDeviceType(string $userId, string $deviceType): int
    {
        $key = "device:$userId:$deviceType";
        $cachedData = $this->cache->get($key);
        if ($cachedData) {
            return (int) $cachedData;
        }

        $deviceCount = $this->repository->countByDeviceType($userId, $deviceType);
        $this->cache->set($key, $deviceCount);

        return $deviceCount;
    }
    
}
