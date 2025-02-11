<?php

declare(strict_types=1);

namespace Venkatesanchinna\LogMonitor\Support\Providers\Concerns;

/**
 * Trait     HasTranslations
 *
 * @author   Venkatesan C <venkatesangee@gmail.com>
 */
trait HasTranslations
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the translations' path.
     *
     * @return string
     */
    protected function getTranslationsPath(): string
    {
        return $this->getBasePath().DIRECTORY_SEPARATOR.'translations';
    }

    /**
     * Get the destination views path.
     *
     * @return string
     */
    protected function getTranslationsDestinationPath(): string
    {
        return $this->app['path.lang'].DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.$this->getPackageName();
    }

    /**
     * Publish the translations.
     *
     * @param  string|null  $path
     */
    protected function publishTranslations(?string $path = null): void
    {
        $this->publishes([
            $this->getTranslationsPath() => $path ?: $this->getTranslationsDestinationPath(),
        ], $this->getPublishedTags('translations'));
    }

    /**
     * Load the translations files.
     */
    protected function loadTranslations(): void
    {
        $packagePath = $this->getTranslationsPath();
        $vendorPath = $this->getTranslationsDestinationPath();

        $this->loadTranslationsFrom($packagePath, $this->getPackageName());
        // $this->loadJsonTranslationsFrom(file_exists($vendorPath) ? $vendorPath : $packagePath);
    }
}
