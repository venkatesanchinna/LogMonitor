<?php

declare(strict_types=1);

namespace Venkatesanchinna\LogMonitor\Support\Providers;

use Venkatesanchinna\LogMonitor\Support\Providers\Concerns\InteractsWithApplication;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

/**
 * Class     ServiceProvider
 *
 * @author   Venkatesan C <venkatesangee@gmail.com>
 */
abstract class ServiceProvider extends IlluminateServiceProvider
{
    /* -----------------------------------------------------------------
     |  Traits
     | -----------------------------------------------------------------
     */

    use InteractsWithApplication;
}
