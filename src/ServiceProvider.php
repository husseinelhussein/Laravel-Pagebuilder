<?php

namespace HansSchouten\LaravelPageBuilder;

use HansSchouten\LaravelPageBuilder\Commands\CreateTheme;
use HansSchouten\LaravelPageBuilder\Commands\PublishDemo;
use HansSchouten\LaravelPageBuilder\Commands\PublishTheme;
use PHPageBuilder\PHPageBuilder;
use Exception;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     * @throws Exception
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/admin.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/front.php');
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');
        $this->loadViewsFrom(__DIR__ . '/Resources/views', 'pagebuilder');
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateTheme::class,
                PublishTheme::class,
                PublishDemo::class,
            ]);
        } elseif (empty(config('pagebuilder'))) {
            throw new Exception("No PHPageBuilder config found, please run: php artisan vendor:publish --provider=\"HansSchouten\LaravelPageBuilder\ServiceProvider\" --tag=config");
        }

        // register singleton phpPageBuilder (this ensures phpb_ helpers have the right config without first manually creating a PHPageBuilder instance)
        $this->app->singleton('phpPageBuilder', function($app) {
            return new PHPageBuilder(config('pagebuilder') ?? []);
        });
        $this->app->make('phpPageBuilder');

        $this->publishes([
            __DIR__ . '/../config/pagebuilder.php' => config_path('pagebuilder.php'),
        ], 'config');
        $this->copyAssets();
        $this->publishDemoTheme();
    }

    /**
     *
     */
    public function copyAssets(){
        $src = base_path('vendor/hansschouten/phpagebuilder/dist/pagebuilder/');
        $dest = public_path(config('pagebuilder.general.assets_url') . '/pagebuilder');
        $this->publishes([$src => $dest],'assets');
    }

    /**
     * Publishes the demo theme files and assets.
     *
     */
    public function publishDemoTheme(){
        // publish demo theme:
        $themes_path = config('pagebuilder.theme.folder') . '/demo';
        $this->publishes([
            __DIR__ . '/../themes/demo' => $themes_path,
        ], 'demo-theme');

        // publish demo theme assets:
        $themes_assets_path = public_path(config('pagebuilder.general.assets_url') . '/demo');
        $stop = null;
        $this->publishes([
            __DIR__ . '/../themes/demo/public' => $themes_assets_path,
        ], 'demo-theme');
    }
}
