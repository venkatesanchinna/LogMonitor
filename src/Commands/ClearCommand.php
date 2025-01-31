<?php

declare(strict_types=1);

namespace Venkatesanchinna\LogMonitor\Commands;

/**
 * Class     ClearCommand
 *
 * @author   Venkatesan C <venkatesangee@gmail.com>
 */
class ClearCommand extends Command
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log-monitor:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all generated log files';

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->confirm('This will delete all the log files, Do you wish to continue?')) {
            $this->logMonitor->clear();
            $this->info('Successfully cleared the logs!');
        }
    }
}
