<?php
namespace Qtvhao\DeviceAccessControl\Core\UseCases;

use Qtvhao\DeviceAccessControl\Core\Interfaces\DeviceAccessRepositoryInterface;

class UpdateDeviceAccessTimeUseCase
{
    protected $deviceRepository;

    public function __construct(DeviceAccessRepositoryInterface $deviceRepository)
    {
        $this->deviceRepository = $deviceRepository;
    }

    public function execute(string $deviceId, string $userId): bool
    {
        // Lấy thời gian hiện tại
        $currentTime = new \DateTime();

        // Cập nhật thời gian truy cập cuối cùng của thiết bị
        return $this->deviceRepository->updateLastAccessTime($deviceId, $userId, $currentTime);
    }
}
