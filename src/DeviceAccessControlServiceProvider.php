<?php
namespace Qtvhao\DeviceAccessControl;


use Illuminate\Support\ServiceProvider;
use Qtvhao\DeviceAccessControl\Repository\DeviceAccessRepository;
use Qtvhao\DeviceAccessControl\Core\Interfaces\DeviceAccessRepositoryInterface;
use Qtvhao\DeviceAccessControl\Model\Device as DeviceModel;
use Illuminate\Routing\Router;
use Qtvhao\DeviceAccessControl\Middleware\DeviceAccessMiddleware;
use Qtvhao\DeviceAccessControl\Core\UseCases\CheckDeviceLimitUseCase;

class DeviceAccessControlServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(DeviceAccessRepositoryInterface::class, function ($app) {
            return new DeviceAccessRepository(new DeviceModel());
        });
        $this->app->bind(CheckDeviceLimitUseCase::class, function ($app) {
            return new CheckDeviceLimitUseCase(
                $app->make(DeviceAccessRepositoryInterface::class),
                config('device_access_control.device_limit', 1) // lấy device_limit từ config, default là 1 nếu không có
            );
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