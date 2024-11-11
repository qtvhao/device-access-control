<?php
namespace Qtvhao\DeviceAccessControl\Core\UseCases;

use Qtvhao\DeviceAccessControl\Core\Data\DeviceData;
use Qtvhao\DeviceAccessControl\Core\Interfaces\DeviceAccessRepositoryInterface;
use Qtvhao\DeviceAccessControl\Core\Entities\Device;

class AddNewDeviceUseCase
{
    private $deviceRepository;

    public function __construct(DeviceAccessRepositoryInterface $deviceRepository)
    {
        $this->deviceRepository = $deviceRepository;
    }

    public function execute(DeviceData $deviceData): Device
    {
        $device = $this->deviceRepository->save($deviceData);

        return $device;
    }
}
