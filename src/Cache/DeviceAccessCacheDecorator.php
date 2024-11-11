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
        throw new \Exception("Not implemented. This method should not be called.");
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
    
}
