<?php
namespace Qtvhao\DeviceAccessControl\Core\UseCases;

use Qtvhao\DeviceAccessControl\Core\UseCases\CheckDeviceLimitUseCase;
use Qtvhao\DeviceAccessControl\Core\UseCases\CheckExistingDeviceUseCase;
use Qtvhao\DeviceAccessControl\Core\UseCases\AddNewDeviceUseCase;
use Qtvhao\DeviceAccessControl\Core\Data\DeviceData;
use Qtvhao\DeviceAccessControl\Events\DeviceAccessUpdatedEvent;
use Illuminate\Contracts\Events\Dispatcher;

class DeviceAccessOrchestrator
{
    protected $checkExistingDeviceUseCase;
    protected $checkDeviceLimitUseCase;
    protected $addNewDeviceUseCase;
    protected $updateDeviceAccessTimeUseCase;
    protected $eventDispatcher;

    public function __construct(
        CheckExistingDeviceUseCase $checkExistingDeviceUseCase,
        CheckDeviceLimitUseCase $checkDeviceLimitUseCase,
        AddNewDeviceUseCase $addNewDeviceUseCase,
        UpdateDeviceAccessTimeUseCase $updateDeviceAccessTimeUseCase,
        Dispatcher $eventDispatcher
    ) {
        $this->checkExistingDeviceUseCase = $checkExistingDeviceUseCase;
        $this->checkDeviceLimitUseCase = $checkDeviceLimitUseCase;
        $this->addNewDeviceUseCase = $addNewDeviceUseCase;
        $this->updateDeviceAccessTimeUseCase = $updateDeviceAccessTimeUseCase;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function execute(DeviceData $deviceData): bool
    {
        $deviceId = $deviceData->getDeviceUuid();
        $deviceType = $deviceData->getDeviceType();
        $userId = $deviceData->getUserId();
        // Step 1: Kiểm tra thiết bị đã tồn tại chưa
        if ($this->checkExistingDeviceUseCase->execute($deviceId, $userId)) {
            // Thiết bị đã tồn tại, cập nhật thời gian truy cập cuối cùng
            $this->eventDispatcher->dispatch(new DeviceAccessUpdatedEvent($deviceId, $userId));
            return true;
        }

        // Step 2: Kiểm tra xem loại thiết bị có vượt giới hạn hay không
        if (!$this->checkDeviceLimitUseCase->execute($userId, $deviceType)) {
            // Loại thiết bị đã đạt giới hạn, từ chối truy cập
            return false;
        }

        // Step 3: Thêm thiết bị mới vào hệ thống
        $this->addNewDeviceUseCase->execute($deviceData);

        // Thiết bị mới đã được thêm, cho phép truy cập
        return true;
    }
}