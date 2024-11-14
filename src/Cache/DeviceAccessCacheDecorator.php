<?php
namespace Qtvhao\DeviceAccessControl\Cache;

use Qtvhao\DeviceAccessControl\Core\Data\DeviceData;
use Qtvhao\DeviceAccessControl\Core\Entities\Device;
use Qtvhao\DeviceAccessControl\Core\Interfaces\DeviceAccessRepositoryInterface;
use Psr\Log\LoggerInterface;
use Redis;

class DeviceAccessCacheDecorator implements DeviceAccessRepositoryInterface
{
    private DeviceAccessRepositoryInterface $repository;
    private Redis $cache;
    private LoggerInterface $logger;

    public function __construct(DeviceAccessRepositoryInterface $repository, Redis $cache, LoggerInterface $logger)
    {
        $this->repository = $repository;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    public function updateLastAccessTime(string $deviceId, string $userId, \DateTime $lastAccessTime): bool
    {
        $result = $this->repository->updateLastAccessTime($deviceId, $userId, $lastAccessTime);

        return $result;
    }

    public function save(DeviceData $device): Device
    {
        // Save device data and update cache
        $savedDevice = $this->repository->save($device);

        try {
            $cacheKey = $this->getDeviceCacheKey($device->getUserId(), $device->getDeviceId());
            $this->cache->set($cacheKey, $savedDevice, ['EX' => 3600]); // Cache for 1 hour
        } catch (\Exception $e) {
            // Log lỗi và tiếp tục trả về kết quả từ repository
            $this->logger->error("Failed to save device data in cache", [
                'error' => $e->getMessage(),
                'deviceId' => $device->getDeviceId(),
                'userId' => $device->getUserId()
            ]);
        }

        return $savedDevice;
    }

    public function findByDeviceId(string $deviceId, string $userId): Device
    {
        try {
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
        } catch (\Exception $e) {
            // Log lỗi và truy xuất từ repository nếu cache lỗi
            $this->logger->error("Failed to retrieve device data from cache", [
                'error' => $e->getMessage(),
                'deviceId' => $deviceId,
                'userId' => $userId
            ]);
        } //

        $device = $this->repository->findByDeviceId($deviceId, $userId);
        try {
            $this->cache->set("device:$deviceId:user:$userId", json_encode([
                'deviceId' => $device->getDeviceId(),
                'deviceType' => $device->getDeviceType()
            ]), ['EX' => 3600]); // Cache for 1 hour
        } catch (\Exception $e) {
            $this->logger->error("Failed to cache device data after retrieving from repository", [
                'error' => $e->getMessage(),
                'deviceId' => $deviceId,
                'userId' => $userId
            ]);
        }

        return $device;
    }

    public function countByDeviceType(string $userId, string $deviceType): int
    {
        $key = $this->getDeviceCountCacheKey($userId, $deviceType);
        try {
            $cachedData = $this->cache->get($key);
            if ($cachedData) {
                return (int) $cachedData;
            }
        } catch (\Exception $e) {
            $this->logger->error("Failed to retrieve device count from cache", [
                'error' => $e->getMessage(),
                'userId' => $userId,
                'deviceType' => $deviceType
            ]);
        } //

        $deviceCount = $this->repository->countByDeviceType($userId, $deviceType);
        try {
            $this->cache->set($key, $deviceCount, ['EX' => 3600]); // Cache for 1 hour
        } catch (\Exception $e) {
            $this->logger->error("Failed to cache device count after retrieving from repository", [
                'error' => $e->getMessage(),
                'userId' => $userId,
                'deviceType' => $deviceType
            ]);
        }

        return $deviceCount;
    }
    
    private function getDeviceCacheKey(string $userId, string $deviceId): string
    {
        return "device:user:{$userId}:device:{$deviceId}";
    }

    private function getDeviceCountCacheKey(string $userId, string $deviceType): string
    {
        return "device_count:user:{$userId}:type:{$deviceType}";
    }
}
