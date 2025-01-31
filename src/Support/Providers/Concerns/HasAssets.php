<?php

declare(strict_types=1);

namespace Venkatesanchinna\LogMonitor\Support\Providers\Concerns;

/**
 * Trait     HasAssets
 *
 * @author   Venkatesan C <venkatesangee@gmail.com>
 */
trait HasAssets
{
    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the assets path.
     *
     * @return string
     */
    protected function getAssetsFolder(): string
    {
        return realpath($this->getBasePath().DIRECTORY_SEPARATOR.'assets');
    }

    /**
     * Get the assets destination path.
     *
     * @return string
     */
    protected function assetsDestinationPath(): string
    {
        return base_path('assets'.DIRECTORY_SEPARATOR.$this->getPackageName());
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Publish the assets.
     */
    protected function publishAssets(): void
    {
        $this->publishes([
            $this->getAssetsFolder() => $this->assetsDestinationPath(),
        ], $this->getPublishedTags('assets'));
    }
}
