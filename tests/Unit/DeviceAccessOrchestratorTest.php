<?php
namespace Qtvhao\DeviceAccessControl\Tests\Unit;

use Qtvhao\DeviceAccessControl\Core\UseCases\CheckExistingDeviceUseCase;
use Qtvhao\DeviceAccessControl\Core\UseCases\CheckDeviceLimitUseCase;
use Qtvhao\DeviceAccessControl\Core\UseCases\AddNewDeviceUseCase;
use Qtvhao\DeviceAccessControl\Core\UseCases\DeviceAccessOrchestrator;
use Qtvhao\DeviceAccessControl\Core\UseCases\UpdateDeviceAccessTimeUseCase;
use Qtvhao\DeviceAccessControl\Core\Data\DeviceData;
use PHPUnit\Framework\TestCase;
use Mockery;

class DeviceAccessOrchestratorTest extends TestCase
{
    private $userId = 123;
    public function test_allow_access_if_device_already_exists()
    {
        $checkExistingDeviceMock = Mockery::mock(CheckExistingDeviceUseCase::class);
        $checkDeviceLimitMock = Mockery::mock(CheckDeviceLimitUseCase::class);
        $addNewDeviceMock = Mockery::mock(AddNewDeviceUseCase::class);
        $updateDeviceAccessTimeUseCase = Mockery::mock(UpdateDeviceAccessTimeUseCase::class);

        $updateDeviceAccessTimeUseCase->shouldReceive('execute');

        $deviceData = new DeviceData(
            deviceId: 'device123',
            deviceType: 'Mobile',
            deviceName: 'Mobile Device',
            userId: $this->userId
        );

        // Mock device exists
        $checkExistingDeviceMock->shouldReceive('execute')
                                ->with($deviceData->getDeviceId(), $deviceData->getUserId())
                                ->andReturn(true);

        $orchestrator = new DeviceAccessOrchestrator($checkExistingDeviceMock, $checkDeviceLimitMock, $addNewDeviceMock, $updateDeviceAccessTimeUseCase);
        
        // Execute the orchestrator method
        $result = $orchestrator->execute($deviceData);
        
        // Assert that access is allowed since the device already exists
        $this->assertTrue($result, "Access should be allowed if the device already exists.");
    }

    public function test_deny_access_if_device_limit_exceeded()
    {
        $checkExistingDeviceMock = Mockery::mock(CheckExistingDeviceUseCase::class);
        $checkDeviceLimitMock = Mockery::mock(CheckDeviceLimitUseCase::class);
        $addNewDeviceMock = Mockery::mock(AddNewDeviceUseCase::class);
        $updateDeviceAccessTimeUseCase = Mockery::mock(UpdateDeviceAccessTimeUseCase::class);

        $deviceData = new DeviceData(
            deviceId: 'device123',
            deviceType: 'Mobile',
            deviceName: 'Mobile Device',
            userId: $this->userId
        );
        // Mock device does not exist
        $checkExistingDeviceMock->shouldReceive('execute')
                                ->with($deviceData->getDeviceId(), $deviceData->getUserId())
                                ->andReturn(false);

        // Mock device limit exceeded
        $checkDeviceLimitMock->shouldReceive('execute')
                             ->with($this->userId, 'Mobile')
                             ->andReturn(false);

        $orchestrator = new DeviceAccessOrchestrator($checkExistingDeviceMock, $checkDeviceLimitMock, $addNewDeviceMock, $updateDeviceAccessTimeUseCase);
        
        // Execute the orchestrator method
        $result = $orchestrator->execute($deviceData);
        
        // Assert that access is denied since the device limit is exceeded
        $this->assertFalse($result, "Access should be denied if the device limit for that type is exceeded.");
    }

    public function test_allow_access_and_add_new_device_if_limit_not_exceeded()
    {
        $checkExistingDeviceMock = Mockery::mock(CheckExistingDeviceUseCase::class);
        $checkDeviceLimitMock = Mockery::mock(CheckDeviceLimitUseCase::class);
        $addNewDeviceMock = Mockery::mock(AddNewDeviceUseCase::class);
        $updateDeviceAccessTimeUseCase = Mockery::mock(UpdateDeviceAccessTimeUseCase::class);

        $deviceData = new DeviceData(
            deviceId: 'device123',
            deviceType: 'Mobile',
            deviceName: 'Mobile Device',
            userId: $this->userId
        );

        // Mock device does not exist
        $checkExistingDeviceMock->shouldReceive('execute')
                                ->with($deviceData->getDeviceId(), $deviceData->getUserId())
                                ->andReturn(false);

        // Mock device limit not exceeded
        $checkDeviceLimitMock->shouldReceive('execute')
                             ->with($this->userId, $deviceData->getDeviceType())
                             ->andReturn(true);

        // Expect addNewDeviceUseCase to be called with the correct parameters
        $addNewDeviceMock->shouldReceive('execute')
                         ->with(Mockery::on(function (DeviceData $deviceDataToMake) use ($deviceData) {
                             return $deviceData instanceof DeviceData &&
                                    $deviceData->getDeviceId() === $deviceDataToMake->getDeviceId() &&
                                    $deviceData->getDeviceType() === $deviceDataToMake->getDeviceType() &&
                                    $deviceData->getUserId() === $this->userId;
                         }))
                         ->once();

        $orchestrator = new DeviceAccessOrchestrator($checkExistingDeviceMock, $checkDeviceLimitMock, $addNewDeviceMock, $updateDeviceAccessTimeUseCase);
        
        // Execute the orchestrator method
        $result = $orchestrator->execute($deviceData);
        
        // Assert that access is allowed and a new device was added
        $this->assertTrue($result, "Access should be allowed if device limit is not exceeded, and the new device should be added.");
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
