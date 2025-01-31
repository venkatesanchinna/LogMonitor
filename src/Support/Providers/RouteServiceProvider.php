<?php

declare(strict_types=1);

namespace Venkatesanchinna\LogMonitor\Support\Providers;

use Venkatesanchinna\LogMonitor\Support\Routing\Concerns\RegistersRouteClasses;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as IlluminateServiceProvider;

/**
 * Class     RouteServiceProvider
 *
 * @author   Venkatesan C <venkatesangee@gmail.com>
 */
abstract class RouteServiceProvider extends IlluminateServiceProvider
{
    /* -----------------------------------------------------------------
     |  Traits
     | -----------------------------------------------------------------
     */

    use RegistersRouteClasses;
}
