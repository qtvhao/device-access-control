<?php
namespace Qtvhao\DeviceAccessControl;


use Illuminate\Support\ServiceProvider;
use Qtvhao\DeviceAccessControl\Repository\DeviceAccessRepository;
use Qtvhao\DeviceAccessControl\Core\Interfaces\DeviceAccessRepositoryInterface;
use Qtvhao\DeviceAccessControl\Model\Device as DeviceModel;
use Illuminate\Routing\Router;
use Qtvhao\DeviceAccessControl\Middleware\DeviceAccessMiddleware;

class DeviceAccessControlServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(DeviceAccessRepositoryInterface::class, function ($app) {
            return new DeviceAccessRepository(new DeviceModel());
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        // Register middleware
        $router->aliasMiddleware('device.access', DeviceAccessMiddleware::class);
        $this->publishesMigrations([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'device-access-control-migrations');
    }
}