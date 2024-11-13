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

    public function updateLastAccessTime(string $deviceId, string $userId, \DateTime $lastAccessTime): bool
    {
        $result = $this->repository->updateLastAccessTime($deviceId, $userId, $lastAccessTime);

        return $result;
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

    public function findByDeviceId(string $deviceId, string $userId): Device
    {
        $cachedData = $this->cache->get("device:$deviceId:user:$userId");
        if ($cachedData) {
            $device = json_decode($cachedData, true);
            return new Device(
                deviceId: $device['deviceId'],
                deviceType: $device['deviceType'],
                deviceName: 'Mobile Device',
                userId: $userId,
            );
        }

        $device = $this->repository->findByDeviceId($deviceId, $userId);
        $this->cache->set("device:$deviceId:user:$userId", json_encode([
            'deviceId' => $device->getDeviceId(),
            'deviceType' => $device->getDeviceType()
        ]), ['EX' => 3600]); // Cache for 1 hour

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
