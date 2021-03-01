<?php

namespace HansSchouten\LaravelPageBuilder\Providers;

use Illuminate\Support\ServiceProvider;
use HansSchouten\LaravelPageBuilder\Commands\CreateTheme;
use HansSchouten\LaravelPageBuilder\Commands\PublishDemo;
use HansSchouten\LaravelPageBuilder\Commands\PublishTheme;
use PHPageBuilder\PHPageBuilder;
use Exception;
use DirectoryIterator;
class LaravelPageBuilderServiceProvider extends ServiceProvider
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
        $this->loadRoutesFrom(__DIR__ . '/../Http/admin.php');
        $this->loadRoutesFrom(__DIR__ . '/../Http/front.php');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
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
            __DIR__ . '/../../publishable/config/pagebuilder.php' => config_path('pagebuilder.php'),
        ], 'config');
        $this->copyAssets();
        $this->publishDemoTheme();
        $this->loadThemesClasses();
        $this->publishPublicThemesFiles();
        $this->addThemesViews();
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
            __DIR__ . '/../../publishable/themes/demo' => $themes_path,
        ], 'demo-theme');

        // publish demo theme assets:
        $themes_assets_path = public_path(config('pagebuilder.general.assets_url') . '/demo');
        $stop = null;
        $this->publishes([
            __DIR__ . '/../../publishable/themes/demo/public' => $themes_assets_path,
        ], 'demo-theme');
    }

    /**
     * Auto-loads themes classes files:
     *
     */
    public function loadThemesClasses(){
        $themes_path = config('pagebuilder.theme.folder');
        // iterate through each theme folder looking for "vendor" dir
        $themes = new DirectoryIterator($themes_path);
        /** @var DirectoryIterator $theme */
        foreach ($themes as $theme) {
            if($theme->isDir()){
                // look for "vendor" folder:
                $path = $theme->getPathname() . '/vendor';
                $vendor_contents = null;
                try {
                    $vendor_contents = new DirectoryIterator($path);

                } catch (\Exception $e){
                    // probably not readable or doesn't exist
                }
                if (!$vendor_contents) {
                    continue;
                }
                // vendor exists, check vendor/autoload.php file:
                if(file_exists($path . '/autoload.php')){
                    // exists, require it
                    require_once $path . '/autoload.php';
                }
            }
        }
    }

    /**
     * Publishes the demo theme files and assets.
     *
     */
    public function publishPublicThemesFiles(){
        // look for public files in all themes:
        $publishables = [];
        $themes_path = config('pagebuilder.theme.folder');
        $themes = new DirectoryIterator($themes_path);
        /** @var DirectoryIterator $theme */
        foreach ($themes as $theme) {
            if($theme->isDir()){
                // look for publishable folder:
                $path = $theme->getPathname() . '/publishable';
                $public_contents = null;
                try {
                    $public_contents = new DirectoryIterator($path);

                } catch (\Exception $e){
                    // probably not readable or doesn't exist
                }
                if (!$public_contents) {
                    continue;
                }
                $publishables[$path] = public_path(config('pagebuilder.general.assets_url') . '/' . $theme->getFilename());
            }
        }
        $this->publishes($publishables,'assets');
    }

    public function addThemesViews(){
        $themes_path = config('pagebuilder.theme.folder');
        $views = [
            __DIR__ . '/../Resources/views',
            $themes_path,
        ];
        view()->addNamespace('pagebuilder', $views);
    }
}
