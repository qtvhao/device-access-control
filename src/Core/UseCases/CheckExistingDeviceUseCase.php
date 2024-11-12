<?php
namespace Qtvhao\DeviceAccessControl\Core\UseCases;

use Qtvhao\DeviceAccessControl\Core\Interfaces\DeviceAccessRepositoryInterface;

class CheckExistingDeviceUseCase
{
    private $deviceRepository;

    public function __construct(DeviceAccessRepositoryInterface $deviceRepository)
    {
        $this->deviceRepository = $deviceRepository;
    }

    public function execute($deviceId)
    {
        $device = $this->deviceRepository->findByDeviceId($deviceId);
        return $device !== null and $device->getDeviceId() === $deviceId;
    }
}