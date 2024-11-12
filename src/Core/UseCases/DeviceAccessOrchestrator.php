<?php
namespace Qtvhao\DeviceAccessControl\Core\UseCases;

use Qtvhao\DeviceAccessControl\Core\UseCases\CheckDeviceLimitUseCase;
use Qtvhao\DeviceAccessControl\Core\UseCases\CheckExistingDeviceUseCase;
use Qtvhao\DeviceAccessControl\Core\UseCases\AddNewDeviceUseCase;
use Qtvhao\DeviceAccessControl\Core\Data\DeviceData;

class DeviceAccessOrchestrator
{
    protected $checkExistingDeviceUseCase;
    protected $checkDeviceLimitUseCase;
    protected $addNewDeviceUseCase;

    public function __construct(
        CheckExistingDeviceUseCase $checkExistingDeviceUseCase,
        CheckDeviceLimitUseCase $checkDeviceLimitUseCase,
        AddNewDeviceUseCase $addNewDeviceUseCase
    ) {
        $this->checkExistingDeviceUseCase = $checkExistingDeviceUseCase;
        $this->checkDeviceLimitUseCase = $checkDeviceLimitUseCase;
        $this->addNewDeviceUseCase = $addNewDeviceUseCase;
    }

    /**
     * Execute the device access check.
     *
     * @param string $userId
     * @param string $deviceId
     * @param string $deviceType
     * @return bool
     */
    public function execute($userId, $deviceId, $deviceType)
    {
        // Step 1: Kiểm tra thiết bị đã tồn tại chưa
        if ($this->checkExistingDeviceUseCase->execute($deviceId)) {
            // Thiết bị đã tồn tại, cho phép truy cập
            return true;
        }

        // Step 2: Kiểm tra xem loại thiết bị có vượt giới hạn hay không
        if (!$this->checkDeviceLimitUseCase->execute($userId, $deviceType)) {
            // Loại thiết bị đã đạt giới hạn, từ chối truy cập
            return false;
        }

        // Step 3: Thêm thiết bị mới vào hệ thống
        $this->addNewDeviceUseCase->execute(new DeviceData(
            deviceId: $deviceId,
            deviceType: $deviceType,
            userId: $userId
        ));

        // Thiết bị mới đã được thêm, cho phép truy cập
        return true;
    }
}