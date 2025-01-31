<?php

declare (strict_types = 1);

namespace Venkatesanchinna\LogMonitor;

use Venkatesanchinna\LogMonitor\Support\Providers\PackageServiceProvider;

/**
 * Class     LogMonitorServiceProvider
 *
 * @author   Venkatesan C <venkatesangee@gmail.com>
 */
class LogMonitorServiceProvider extends PackageServiceProvider
{
    /* -----------------------------------------------------------------
    |  Properties
    | -----------------------------------------------------------------
     */

    /**
     * Package name.
     *
     * @var string
     */
    protected $package = 'log-monitor';

    /* -----------------------------------------------------------------
    |  Main Methods
    | -----------------------------------------------------------------
     */

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        parent::register();

        $this->registerConfig();

        $this->registerProvider(Providers\RouteServiceProvider::class);

        $this->registerCommands([
            Commands\PublishCommand::class,
            Commands\StatsCommand::class,
            Commands\CheckCommand::class,
            Commands\ClearCommand::class,
        ]);
    }

    /**
     * Boot the service provider.
     */
    public function boot(): void
    {

        $this->loadTranslations();
        $this->loadViews();

        $this->shareAuthorAndPakcage();
        if ($this->app->runningInConsole()) {
            $this->publishConfig();
            $this->publishTranslations();
            $this->publishViews();
            // Publish the compiled CSS file
            $this->publishes([
                __DIR__ . '/../resources/assets/css/log.css' => public_path('vendor/log-monitor/assets/css/log.css'),
                __DIR__ . '/../resources/assets/js/logs.js'  => public_path('vendor/log-monitor/assets/js/logs.js'),
            ], 'log-monitor-assets');
        }
    }
    private function shareAuthorAndPakcage()
    {
        \View::share('packageName', $this->getPackageShortName());

        // Share the author's name with all views
        \View::share('authorName', $this->getAuthorName());
    }
}
