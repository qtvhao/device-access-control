<?php
namespace Qtvhao\DeviceAccessControl\Providers;


use Illuminate\Support\ServiceProvider;
use Qtvhao\DeviceAccessControl\Repository\DeviceAccessRepository;
use Qtvhao\DeviceAccessControl\Core\Interfaces\DeviceAccessRepositoryInterface;

class DeviceAccessControlServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(DeviceAccessRepositoryInterface::class, function ($app) {
            return new DeviceAccessRepository();
        });
    }
    public function boot()
    {
        // Register the migrations
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
    }
}