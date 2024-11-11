<?php
namespace Qtvhao\DeviceAccessControl\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Qtvhao\DeviceAccessControl\Core\Interfaces\DeviceAccessRepositoryInterface;
use Qtvhao\DeviceAccessControl\Cache\DeviceAccessCacheDecorator;
use Qtvhao\DeviceAccessControl\Core\Entities\Device;
use Mockery;
use Redis;

class DeviceAccessCacheDecoratorTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();  // Ensure mockery expectations are cleared after each test
    }

    public function test_findByDeviceId_with_cache_hit()
    {
        // Arrange: Setup the device data and mock the Redis client
        $deviceId = 'device123';
        $cachedData = ['deviceId' => 'device123', 'deviceType' => 'Web'];
        $redisMock = Mockery::mock(Redis::class);

        // Simulate a cache hit: Redis `get` should return cached data
        $redisMock->shouldReceive('get')
                  ->with("device:$deviceId")
                  ->andReturn(json_encode($cachedData));

        $repositoryMock = Mockery::mock(DeviceAccessRepositoryInterface::class);

        // Instantiate the decorator with mocked dependencies
        $decorator = new DeviceAccessCacheDecorator($repositoryMock, $redisMock);

        // Act: Execute the findByDeviceId method
        $result = $decorator->findByDeviceId($deviceId);

        // Assert: Check if data was retrieved from cache correctly
        $this->assertInstanceOf(Device::class, $result, "Data should be fetched from cache on cache hit.");
        $this->assertEquals($result->getDeviceId(), $cachedData['deviceId'], "Device ID should match cached data.");
        $this->assertEquals($result->getDeviceType(), $cachedData['deviceType'], "Device type should match cached data.");
    }

    public function test_findByDeviceId_with_cache_miss()
    {
        // Arrange: Setup the device data and mock the Redis client
        $deviceId = 'device456';
        $device = new Device('device456', 'Mobile');
        $redisMock = Mockery::mock(Redis::class);

        // Simulate a cache miss: Redis `get` returns null, leading to repository fetch
        $redisMock->shouldReceive('get')
                  ->with("device:$deviceId")
                  ->andReturn(null);

        // Redis `set` should be called to cache the result from the repository
        $redisMock->shouldReceive('set')
                  ->with("device:$deviceId", json_encode(['deviceId' => 'device456', 'deviceType' => 'Mobile']))
                  ->once();

        // Mock the repository to return a Device object on cache miss
        $repositoryMock = Mockery::mock(DeviceAccessRepositoryInterface::class);
        $repositoryMock->shouldReceive('findByDeviceId')
                       ->with($deviceId)
                       ->andReturn(new Device('device456', 'Mobile'));

        // Instantiate the decorator with mocked dependencies
        $decorator = new DeviceAccessCacheDecorator($repositoryMock, $redisMock);

        // Act: Execute the findByDeviceId method
        $result = $decorator->findByDeviceId($deviceId);

        // Assert: Verify the returned result matches the repository data and was cached
        $this->assertInstanceOf(Device::class, $result,  "Data should be fetched from repository on cache miss.");
        $this->assertEquals($result->getDeviceId(), $device->getDeviceId(), "Device ID should match repository data.");
        $this->assertEquals($result->getDeviceType(), $device->getDeviceType(), "Device type should match repository data.");
    }
}
