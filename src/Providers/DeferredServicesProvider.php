<?php

declare (strict_types = 1);

namespace Venkatesanchinna\LogMonitor\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Venkatesanchinna\LogMonitor\Contracts\LogMonitor as LogMonitorContract;
use Venkatesanchinna\LogMonitor\Contracts\Utilities\Factory as FactoryContract;
use Venkatesanchinna\LogMonitor\Contracts\Utilities\Filesystem as FilesystemContract;
use Venkatesanchinna\LogMonitor\Contracts\Utilities\LogChecker as LogCheckerContract;
use Venkatesanchinna\LogMonitor\Contracts\Utilities\LogLevels as LogLevelsContract;
use Venkatesanchinna\LogMonitor\Contracts\Utilities\LogMenu as LogMenuContract;
use Venkatesanchinna\LogMonitor\Contracts\Utilities\LogStyler as LogStylerContract;
use Venkatesanchinna\LogMonitor\LogMonitor;
use Venkatesanchinna\LogMonitor\Support\Providers\ServiceProvider;
use Venkatesanchinna\LogMonitor\Utilities;

/**
 * Class     DeferredServicesProvider
 *
 * @author   Venkatesan C <venkatesangee@gmail.com>
 */
class DeferredServicesProvider extends ServiceProvider implements DeferrableProvider
{
    /* -----------------------------------------------------------------
    |  Main Methods
    | -----------------------------------------------------------------
     */

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->registerLogMonitor();
        $this->registerLogLevels();
        $this->registerStyler();
        $this->registerLogMenu();
        $this->registerFilesystem();
        $this->registerFactory();
        $this->registerChecker();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [
            LogMonitorContract::class,
            LogLevelsContract::class,
            LogStylerContract::class,
            LogMenuContract::class,
            FilesystemContract::class,
            FactoryContract::class,
            LogCheckerContract::class,
        ];
    }

    /* -----------------------------------------------------------------
    |  LogMonitor Utilities
    | -----------------------------------------------------------------
     */

    /**
     * Register the log viewer service.
     */
    private function registerLogMonitor(): void
    {
        $this->singleton(LogMonitorContract::class, LogMonitor::class);
    }

    /**
     * Register the log levels.
     */
    private function registerLogLevels(): void
    {
        $this->singleton(LogLevelsContract::class, function ($app) {
            return new Utilities\LogLevels(
                $app['translator'],
                $app['config']->get('log-monitor.locale')
            );
        });
    }

    /**
     * Register the log styler.
     */
    private function registerStyler(): void
    {
        $this->singleton(LogStylerContract::class, Utilities\LogStyler::class);
    }

    /**
     * Register the log menu builder.
     */
    private function registerLogMenu(): void
    {
        $this->singleton(LogMenuContract::class, Utilities\LogMenu::class);
    }

    /**
     * Register the log filesystem.
     */
    private function registerFilesystem(): void
    {
        $this->singleton(FilesystemContract::class, function ($app) {
            /** @var  \Illuminate\Config\Repository  $config */
            $config     = $app['config'];
            $filesystem = new Utilities\Filesystem($app['files'], $config->get('log-monitor.storage-path'));

            return $filesystem->setPattern(
                $config->get('log-monitor.pattern.prefix', FilesystemContract::PATTERN_PREFIX),
                $config->get('log-monitor.pattern.date', FilesystemContract::PATTERN_DATE),
                $config->get('log-monitor.pattern.extension', FilesystemContract::PATTERN_EXTENSION)
            );
        });
    }

    /**
     * Register the log factory class.
     */
    private function registerFactory(): void
    {
        $this->singleton(FactoryContract::class, Utilities\Factory::class);
    }

    /**
     * Register the log checker service.
     */
    private function registerChecker(): void
    {
        $this->singleton(LogCheckerContract::class, Utilities\LogChecker::class);
    }
}
