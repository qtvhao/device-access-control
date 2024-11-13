<?php

namespace Qtvhao\DeviceAccessControl\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Qtvhao\DeviceAccessControl\Core\UseCases\CheckDeviceLimitUseCase;
use Qtvhao\DeviceAccessControl\Core\Interfaces\DeviceAccessRepositoryInterface;
use Qtvhao\DeviceAccessControl\Core\Enums\DeviceEnums;
use Mockery;

class CheckDeviceLimitUseCaseTest extends TestCase
{
    public function test_allow_access_when_device_type_not_exists()
    {
        // Assume that the repository returns 0 (no device of the same type)
        $deviceRepositoryMock = Mockery::mock(DeviceAccessRepositoryInterface::class);
        $deviceRepositoryMock->shouldReceive('countByDeviceType')
                             ->with('user123', 'Mobile')
                             ->andReturn(0);
        // No device of type 'Mobile' yet

        // Khởi tạo use case với giới hạn thiết bị là 1
        $useCase = new CheckDeviceLimitUseCase($deviceRepositoryMock, [
            DeviceEnums::DEVICE_TYPE_WEB => 1,
            DeviceEnums::DEVICE_TYPE_MOBILE => 1,
            DeviceEnums::DEVICE_TYPE_TABLET => 1,
        ]);
        
        // Thực thi use case
        $result = $useCase->execute('user123', 'Mobile');

        // Kiểm tra rằng truy cập được cho phép
        $this->assertTrue($result, "Access should be allowed when device type not exists");
    }
    public function test_deny_access_when_device_type_exists()
    {
        // Giả lập repository trả về 1 (đã có thiết bị cùng loại)
        $deviceRepositoryMock = Mockery::mock(DeviceAccessRepositoryInterface::class);
        $deviceRepositoryMock->shouldReceive('countByDeviceType')
                             ->with('user123', 'Mobile')
                             ->andReturn(1);
        // One device of type 'Mobile' exists

        // Khởi tạo use case với giới hạn thiết bị là 1
        $useCase = new CheckDeviceLimitUseCase($deviceRepositoryMock, [
            DeviceEnums::DEVICE_TYPE_WEB => 1,
            DeviceEnums::DEVICE_TYPE_MOBILE => 1,
            DeviceEnums::DEVICE_TYPE_TABLET => 1,
        ]);
        
        // Thực thi use case
        $result = $useCase->execute('user123', 'Mobile');

        $this->assertFalse($result, "Access should be denied when device type exists");
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
