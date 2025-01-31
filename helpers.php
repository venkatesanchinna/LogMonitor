<?php

use Venkatesanchinna\LogMonitor\Contracts;

if (!function_exists('log_monitor')) {
    /**
     * Get the LogMonitor instance.
     *
     * @return Venkatesanchinna\LogMonitor\Contracts\LogMonitor
     */
    function log_monitor()
    {
        return app(Contracts\LogMonitor::class);
    }
}

if (!function_exists('log_levels')) {
    /**
     * Get the LogLevels instance.
     *
     * @return Venkatesanchinna\LogMonitor\Contracts\Utilities\LogLevels
     */
    function log_levels()
    {
        return app(Contracts\Utilities\LogLevels::class);
    }
}

if (!function_exists('log_menu')) {
    /**
     * Get the LogMenu instance.
     *
     * @return Venkatesanchinna\LogMonitor\Contracts\Utilities\LogMenu
     */
    function log_menu()
    {
        return app(Contracts\Utilities\LogMenu::class);
    }
}

if (!function_exists('log_styler')) {
    /**
     * Get the LogStyler instance.
     *
     * @return Venkatesanchinna\LogMonitor\Contracts\Utilities\LogStyler
     */
    function log_styler()
    {
        return app(Contracts\Utilities\LogStyler::class);
    }
}
if (!function_exists('laravel_version')) {
    /**
     * Get laravel version or check if the same version
     *
     * @param  string|null $version
     *
     * @return string|bool
     */
    function laravel_version(string $version = null)
    {
        $appVersion = app()->version();

        if (is_null($version)) {
            return $appVersion;
        }

        return substr($appVersion, 0, strlen($version)) === $version;
    }
}

if (!function_exists('route_is')) {
    /**
     * Check if route(s) is the current route.
     *
     * @param  array|string  $routes
     *
     * @return bool
     */
    function route_is($routes): bool
    {
        if (!is_array($routes)) {
            $routes = [$routes];
        }

        /** @var Illuminate\Routing\Router $router */
        $router = app('router');

        return call_user_func_array([$router, 'is'], $routes);
    }
}
if (!function_exists('objectToArray')) {
/**
 * Convert an object or array to an associative array recursively.
 *
 * @param mixed $result The object or array to be converted.
 * @return array The converted associative array.
 */
    function objectToArray($result)
    {
        $array = array();
        if (is_object($result) || is_array($result)) {
            foreach ($result as $key => $value) {
                if (is_object($value) || is_array($value)) {
                    $array[$key] = objectToArray($value);
                } else {
                    $array[$key] = $value;
                }
            }
        }
        return $array;
    }
}
if (!function_exists('getFullMonths')) {
/**
 * Generate an associative array of full month names indexed by their numerical representations (1 to 12).
 *
 * @return array An associative array of month names.
 */
    function getFullMonths()
    {
        // Initialize an empty array to store month data
        $response = array();

        // Loop through months from 1 to 12
        for ($i = 1; $i <= 12; $i++) {
            // Get the full month name and numerical representation for the current month
            $mktime = mktime(0, 0, 0, $i, 1, date('Y'));
            // Add the month to the response array with its numerical representation as the key
            $response[date('m', $mktime)] = date('F', $mktime);
        }

        // Sort the array by keys (numerical representation)
        ksort($response);

        // Return the associative array of full month names
        return $response;
    }
}
