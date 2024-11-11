<?php
namespace Qtvhao\DeviceAccessControl\Providers;


use Illuminate\Support\ServiceProvider;
use Qtvhao\DeviceAccessControl\Repository\DeviceAccessRepository;
use Qtvhao\DeviceAccessControl\Core\Interfaces\DeviceAccessRepositoryInterface;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;


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
        $this->publishes([
            __DIR__.'/../database/migrations/2024_11_11_000000_create_device_table.php' => $this->getMigrationFileName('create_device_table.php'),
        ], 'device-migrations');
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     */
    protected function getMigrationFileName(string $migrationFileName): string
    {
        $timestamp = date('Y_m_d_His');

        $filesystem = $this->app->make(Filesystem::class);

        return Collection::make([$this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR])
            ->flatMap(fn ($path) => $filesystem->glob($path.'*_'.$migrationFileName))
            ->push($this->app->databasePath()."/migrations/{$timestamp}_{$migrationFileName}")
            ->first();
    }
}