<?php

namespace Qtvhao\DeviceAccessControl\Tests\Feature\Repository;

use Tests\TestCase;
use Qtvhao\DeviceAccessControl\Repository\DeviceAccessRepository;
use Qtvhao\DeviceAccessControl\Core\Data\DeviceData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Qtvhao\DeviceAccessControl\Core\Enums\DeviceEnums;
use Qtvhao\DeviceAccessControl\Core\Interfaces\DeviceAccessRepositoryInterface;

class DeviceAccessRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected DeviceAccessRepositoryInterface $deviceAccessRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // Bind interface to the repository implementation
        $this->app->bind(DeviceAccessRepositoryInterface::class, DeviceAccessRepository::class);
        $this->deviceAccessRepository = $this->app->make(DeviceAccessRepositoryInterface::class);
    }

    public function test_it_can_find_a_device_by_id()
    {
        $user = \App\Models\User::factory()->create();
        $deviceUuid = 'device123';

        // Insert a device directly into the database
        $this->deviceAccessRepository->save(new DeviceData(
            deviceUuid: $deviceUuid,
            deviceType: DeviceEnums::DEVICE_TYPE_WEB_BROWSER,
            deviceName: 'Web Browser',
            userId: $user->id
        ));

        $device = $this->deviceAccessRepository->findByDeviceUuid($deviceUuid, $user->id);

        $this->assertNotNull($device);
        $this->assertEquals($deviceUuid, $device->getDeviceUuid());
        $this->assertEquals(DeviceEnums::DEVICE_TYPE_WEB_BROWSER, $device->getDeviceType());
    }

    public function test_it_can_save_a_device()
    {
        $deviceData = new DeviceData(
            deviceUuid: 'device123',
            deviceType: DeviceEnums::DEVICE_TYPE_WEB_BROWSER,
            deviceName: 'Web Browser',
            userId: 1
        );

        $device = $this->deviceAccessRepository->save($deviceData);

        $this->assertNotNull($device);
        $this->assertEquals('device123', $device->getDeviceUuid());
        $this->assertEquals(DeviceEnums::DEVICE_TYPE_WEB_BROWSER, $device->getDeviceType());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}