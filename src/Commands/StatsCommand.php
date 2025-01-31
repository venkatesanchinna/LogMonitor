<?php

declare(strict_types=1);

namespace Venkatesanchinna\LogMonitor\Commands;

use Venkatesanchinna\LogMonitor\Tables\StatsTable;

/**
 * Class     StatsCommand
 *
 * @author   Venkatesan C <venkatesangee@gmail.com>
 */
class StatsCommand extends Command
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name        = 'log-monitor:stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display stats of all logs.';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature   = 'log-monitor:stats';

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Load Data
        $stats   = $this->logMonitor->statsTable('en');

        $rows    = $stats->rows();
        $rows[]  = $this->tableSeparator();
        $rows[]  = $this->prepareFooter($stats);

        // Display Data
        $this->displayLogMonitor();
        $this->table($stats->header(), $rows);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Prepare footer.
     *
     * @param  \Venkatesanchinna\LogMonitor\Tables\StatsTable  $stats
     *
     * @return array
     */
    private function prepareFooter(StatsTable $stats)
    {
        $files = [
            'count' => count($stats->rows()).' log file(s)'
        ];

        return $files + $stats->footer();
    }
}
