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
        $deviceUuid = 'device123';
        $userId = 1;
        $cachedData = ['deviceUuid' => 'device123', 'deviceType' => 'Web'];
        $redisMock = Mockery::mock(Redis::class);

        // Simulate a cache hit: Redis `get` should return cached data
        $redisMock->shouldReceive('get')
            ->with("device:$deviceUuid:user:$userId")
            ->andReturn(json_encode($cachedData));

        $repositoryMock = Mockery::mock(DeviceAccessRepositoryInterface::class);
        $loggerMock = Mockery::mock(\Psr\Log\LoggerInterface::class);

        // Instantiate the decorator with mocked dependencies
        $decorator = new DeviceAccessCacheDecorator($repositoryMock, $redisMock, $loggerMock);

        // Act: Execute the findByDeviceId method
        $result = $decorator->findByDeviceUuid($deviceUuid, $userId);

        // Assert: Check if data was retrieved from cache correctly
        $this->assertInstanceOf(Device::class, $result, "Data should be fetched from cache on cache hit.");
        $this->assertEquals($result->getDeviceUuid(), $cachedData['deviceUuid'], "Device ID should match cached data.");
        $this->assertEquals($result->getDeviceType(), $cachedData['deviceType'], "Device type should match cached data.");
    }

    public function test_findByDeviceId_with_cache_miss()
    {
        // Arrange: Setup the device data and mock the Redis client
        $device = new Device(
            deviceUuid: 'device456',
            deviceType: 'Mobile',
            deviceName: 'Mobile Device',
            userId: 1
        );
        $redisMock = Mockery::mock(Redis::class);

        // Simulate a cache miss: Redis `get` returns null, leading to repository fetch
        $redisMock->shouldReceive('get')
            ->with("device:" . $device->getDeviceUuid() . ":user:" . $device->getUserId())
            ->andReturn(null);

        // Redis `set` should be called to cache the result from the repository
        $redisMock->shouldReceive('set')
            ->with(
                "device:" . $device->getDeviceUuid() . ":user:" . $device->getUserId(),
                json_encode(['deviceUuid' => 'device456', 'deviceType' => 'Mobile']),
                ['EX' => 3600] // Cache for 1 hour
            )
            ->once();

        // Mock the repository to return a Device object on cache miss
        $repositoryMock = Mockery::mock(DeviceAccessRepositoryInterface::class);
        $repositoryMock->shouldReceive('findByDeviceUuid')
            ->with($device->getDeviceUuid(), $device->getUserId())
            ->andReturn(new Device(
                deviceUuid: $device->getDeviceUuid(),
                deviceType: 'Mobile',
                deviceName: 'Mobile Device',
                userId: $device->getUserId()
            ));
        $loggerMock = Mockery::mock(\Psr\Log\LoggerInterface::class);

        // Instantiate the decorator with mocked dependencies
        $decorator = new DeviceAccessCacheDecorator($repositoryMock, $redisMock, $loggerMock);

        // Act: Execute the findByDeviceId method
        $result = $decorator->findByDeviceUuid($device->getDeviceUuid(), $device->getUserId());

        // Assert: Verify the returned result matches the repository data and was cached
        $this->assertInstanceOf(Device::class, $result,  "Data should be fetched from repository on cache miss.");
        $this->assertEquals($result->getDeviceUuid(), $device->getDeviceUuid(), "Device ID should match repository data.");
        $this->assertEquals($result->getDeviceType(), $device->getDeviceType(), "Device type should match repository data.");
    }

    public function test_findByDeviceId_with_different_user_ids()
    {
        // Arrange: Define device data for two different users
        $deviceUuid = 'device789';
        $user1Data = ['deviceUuid' => $deviceUuid, 'deviceType' => 'Tablet', 'userId' => 1];
        $user2Data = ['deviceUuid' => $deviceUuid, 'deviceType' => 'Tablet', 'userId' => 2];

        $redisMock = Mockery::mock(Redis::class);

        // Simulate different cache entries for two users
        $redisMock->shouldReceive('get')
            ->with("device:{$deviceUuid}:user:" . $user1Data['userId'])
            ->andReturn(json_encode($user1Data));

        $redisMock->shouldReceive('get')
            ->with("device:{$deviceUuid}:user:" . $user2Data['userId'])
            ->andReturn(json_encode($user2Data));

        // Repository mock isn't needed here since we're only testing cache hits
        $repositoryMock = Mockery::mock(DeviceAccessRepositoryInterface::class);
        $loggerMock = Mockery::mock(\Psr\Log\LoggerInterface::class);

        // Instantiate the decorator with the mocked Redis client
        $decorator = new DeviceAccessCacheDecorator($repositoryMock, $redisMock, $loggerMock);

        // Act: Execute findByDeviceId with different user IDs
        $resultUser1 = $decorator->findByDeviceUuid($deviceUuid, $user1Data['userId']);
        $resultUser2 = $decorator->findByDeviceUuid($deviceUuid, $user2Data['userId']);

        // Assert: Check that data retrieved matches respective cache entries
        $this->assertInstanceOf(Device::class, $resultUser1, "User 1 data should be fetched from cache.");
        $this->assertEquals($resultUser1->getDeviceUuid(), $user1Data['deviceUuid'], "Device ID should match User 1's cached data.");
        $this->assertEquals($resultUser1->getDeviceType(), $user1Data['deviceType'], "Device type should match User 1's cached data.");
        $this->assertEquals($resultUser1->getUserId(), $user1Data['userId'], "User ID should match User 1's cached data.");

        $this->assertInstanceOf(Device::class, $resultUser2, "User 2 data should be fetched from cache.");
        $this->assertEquals($resultUser2->getDeviceUuid(), $user2Data['deviceUuid'], "Device ID should match User 2's cached data.");
        $this->assertEquals($resultUser2->getDeviceType(), $user2Data['deviceType'], "Device type should match User 2's cached data.");
        $this->assertEquals($resultUser2->getUserId(), $user2Data['userId'], "User ID should match User 2's cached data.");
    }
}
