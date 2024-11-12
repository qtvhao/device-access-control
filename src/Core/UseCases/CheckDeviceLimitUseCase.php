<?php
namespace Qtvhao\DeviceAccessControl\Core\UseCases;

use Qtvhao\DeviceAccessControl\Core\Interfaces\DeviceAccessRepositoryInterface;

class CheckDeviceLimitUseCase {
    private $deviceRepository;
    private $deviceLimit;

    public function __construct(DeviceAccessRepositoryInterface $deviceRepository, int $deviceLimit) {
        $this->deviceRepository = $deviceRepository;
        $this->deviceLimit = $deviceLimit;
    }

    public function execute(string $userId, string $deviceType): bool {
        $deviceCount = $this->deviceRepository->countByDeviceType($userId, $deviceType);
        return $deviceCount < $this->deviceLimit;
    }
}
