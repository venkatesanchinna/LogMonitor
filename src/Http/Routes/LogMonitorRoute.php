<?php

declare(strict_types=1);

namespace Venkatesanchinna\LogMonitor\Http\Routes;

use Venkatesanchinna\LogMonitor\Http\Controllers\LogMonitorController;
use Venkatesanchinna\LogMonitor\Support\Routing\RouteRegistrar;

/**
 * Class     LogMonitorRoute
 *
 * @author   Venkatesan C <venkatesangee@gmail.com>
 */
class LogMonitorRoute extends RouteRegistrar
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Map all routes.
     */
    public function map(): void
    {
        $attributes = (array) config('log-monitor.route.attributes');

        $this->group($attributes, function() {
            $this->name('log-monitor::')->group(function () {
                $this->get('/', [LogMonitorController::class, 'index'])
                     ->name('dashboard'); // log-monitor::dashboard

                $this->mapLogsRoutes();
            });
        });
    }

    /**
     * Map the logs routes.
     */
    private function mapLogsRoutes(): void
    {
        $this->prefix('logs')->name('logs.')->group(function() {
            $this->get('/', [LogMonitorController::class, 'listLogs'])
                 ->name('list'); // log-monitor::logs.list

            $this->delete('delete', [LogMonitorController::class, 'delete'])
                 ->name('delete'); // log-monitor::logs.delete

            $this->prefix('{date}')->group(function() {
                $this->get('/', [LogMonitorController::class, 'show'])
                     ->name('show'); // log-monitor::logs.show

                $this->get('download', [LogMonitorController::class, 'download'])
                     ->name('download'); // log-monitor::logs.download

                $this->get('{level}', [LogMonitorController::class, 'showByLevel'])
                     ->name('filter'); // log-monitor::logs.filter

                $this->get('{level}/search', [LogMonitorController::class, 'search'])
                     ->name('search'); // log-monitor::logs.search
            });
        });
    }
}
