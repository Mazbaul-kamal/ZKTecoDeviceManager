<?php

namespace App\Providers;

use App\Services\ZK\ZKService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class ZKServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ZKService::class, function ($app) {
            $config = config('zkteco');
            
            return new ZKService(
                $config['ip'] ?? '192.168.1.201',
                $config['port'] ?? 4370,
                $config['options'] ?? []
            );
        });
    }
    
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/zkteco.php' => config_path('zkteco.php'),
        ], 'zkteco-config');
        
        // Create log channel
        Log::channel('zkteco');
    }
}