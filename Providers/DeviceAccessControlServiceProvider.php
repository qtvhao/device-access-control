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
        $this->publishesMigrations([
            __DIR__.'/../../database/migrations' => database_path('migrations'),
        ]);
    }
}