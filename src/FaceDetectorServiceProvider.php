<?php

namespace Leomarriel\FaceDetector;

use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;

use Leomarriel\FaceDetector;

class FaceDetectorServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind('FaceDetector', '\Leomarriel\FaceDetector\FaceDetector');
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $configFile = __DIR__ . '/config/facedetector.php';
        ($this->isLumen()) ? $this->app->configure('facedetector') : $this->publishes([$configFile => config_path('facedetector.php')], 'facedetector-config');
        $this->mergeConfigFrom(__DIR__ . '/config/config.php', 'facedetector');
    }

    /**
     * @return bool
     */
    private function isLumen(): bool
    {
        return Str::contains($this->app->version(), 'Lumen');
    }

}