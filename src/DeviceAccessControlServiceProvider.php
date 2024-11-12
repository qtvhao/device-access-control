<?php
namespace Qtvhao\DeviceAccessControl;


use Illuminate\Support\ServiceProvider;
use Qtvhao\DeviceAccessControl\Repository\DeviceAccessRepository;
use Qtvhao\DeviceAccessControl\Core\Interfaces\DeviceAccessRepositoryInterface;
use Qtvhao\DeviceAccessControl\Model\Device as DeviceModel;


class DeviceAccessControlServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(DeviceAccessRepositoryInterface::class, function ($app) {
            return new DeviceAccessRepository(new DeviceModel());
        });
    }
    public function boot()
    {
        $this->publishesMigrations([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'device-access-control-migrations');
    }
}