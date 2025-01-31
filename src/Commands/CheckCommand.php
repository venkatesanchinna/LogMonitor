<?php

declare(strict_types=1);

namespace Venkatesanchinna\LogMonitor\Commands;

use Venkatesanchinna\LogMonitor\Contracts\Utilities\LogChecker as LogCheckerContract;

/**
 * Class     CheckCommand
 *
 * @author   Venkatesan C <venkatesangee@gmail.com>
 */
class CheckCommand extends Command
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
    protected $name      = 'log-monitor:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check all LogMonitor requirements.';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log-monitor:check';

    /* -----------------------------------------------------------------
     |  Getter & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the Log Checker instance.
     *
     * @return \Venkatesanchinna\LogMonitor\Contracts\Utilities\LogChecker
     */
    protected function getChecker()
    {
        return $this->laravel[LogCheckerContract::class];
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->displayLogMonitor();
        $this->displayRequirements();
        $this->displayMessages();
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Display LogMonitor requirements.
     */
    private function displayRequirements()
    {
        $requirements = $this->getChecker()->requirements();

        $this->frame('Application requirements');

        $this->table([
            'Status', 'Message'
        ], [
            [$requirements['status'], $requirements['message']]
        ]);
    }

    /**
     * Display LogMonitor messages.
     */
    private function displayMessages()
    {
        $messages = $this->getChecker()->messages();

        $rows = [];
        foreach ($messages['files'] as $file => $message) {
            $rows[] = [$file, $message];
        }

        if ( ! empty($rows)) {
            $this->frame('LogMonitor messages');
            $this->table(['File', 'Message'], $rows);
        }
    }
}
