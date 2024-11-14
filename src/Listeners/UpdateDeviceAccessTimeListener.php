<?php
namespace Qtvhao\DeviceAccessControl\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Qtvhao\DeviceAccessControl\Events\DeviceAccessUpdatedEvent;
use Qtvhao\DeviceAccessControl\Core\Interfaces\DeviceAccessRepositoryInterface;

class UpdateDeviceAccessTimeListener implements ShouldQueue
{
    protected $deviceRepository;

    public function __construct(DeviceAccessRepositoryInterface $deviceRepository)
    {
        $this->deviceRepository = $deviceRepository;
    }

    public function handle(DeviceAccessUpdatedEvent $event)
    {
        // Xử lý cập nhật thời gian truy cập
        $currentTime = new \DateTime();
        $this->deviceRepository->updateLastAccessTime($event->deviceId, $event->userId, $currentTime);
    }
}
