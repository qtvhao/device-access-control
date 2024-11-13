<?php
namespace Qtvhao\DeviceAccessControl\Core\UseCases;

use Qtvhao\DeviceAccessControl\Core\Interfaces\DeviceAccessRepositoryInterface;

class CheckDeviceLimitUseCase {
    private $deviceRepository;
    private $deviceLimit;

    public function __construct(DeviceAccessRepositoryInterface $deviceRepository, array $deviceLimit) {
        $this->deviceRepository = $deviceRepository;
        $this->deviceLimit = $deviceLimit;
    }

    public function execute(string $userId, string $deviceType): bool {
        if (!isset($this->deviceLimit[$deviceType])) {
            return false; // Nếu loại thiết bị không hợp lệ
        }
    
        $deviceCount = $this->deviceRepository->countByDeviceType($userId, $deviceType);
        return $deviceCount < $this->deviceLimit[$deviceType];
    }
}
