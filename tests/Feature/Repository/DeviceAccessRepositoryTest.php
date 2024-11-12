<?php

namespace Qtvhao\DeviceAccessControl\Tests\Feature\Repository;

use Tests\TestCase;
use Qtvhao\DeviceAccessControl\Repository\DeviceAccessRepository;
use Qtvhao\DeviceAccessControl\Core\Data\DeviceData;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $deviceId = 'device123';

        // Insert a device directly into the database
        $this->deviceAccessRepository->save(new DeviceData(
            deviceId: $deviceId,
            deviceType: 'Web',
            userId: $user->id
        ));

        $device = $this->deviceAccessRepository->findByDeviceId($deviceId);

        $this->assertNotNull($device);
        $this->assertEquals($deviceId, $device->getDeviceId());
        $this->assertEquals('Web', $device->getDeviceType());
    }

    public function test_it_can_save_a_device()
    {
        $deviceData = new DeviceData(
            deviceId: 'device123',
            deviceType: 'Web',
            userId: 1
        );

        $device = $this->deviceAccessRepository->save($deviceData);

        $this->assertNotNull($device);
        $this->assertEquals('device123', $device->getDeviceId());
        $this->assertEquals('Web', $device->getDeviceType());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}