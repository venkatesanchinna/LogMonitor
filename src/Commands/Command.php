<?php

declare(strict_types=1);

namespace Venkatesanchinna\LogMonitor\Commands;

use Venkatesanchinna\LogMonitor\Contracts\LogMonitor as LogMonitorContract;
use Venkatesanchinna\LogMonitor\Support\Console\Command as BaseCommand;

/**
 * Class     Command
 *
 * @author   Venkatesan C <venkatesangee@gmail.com>
 */
abstract class Command extends BaseCommand
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var \Venkatesanchinna\LogMonitor\Contracts\LogMonitor */
    protected $logMonitor;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Create the command instance.
     *
     * @param  \Venkatesanchinna\LogMonitor\Contracts\LogMonitor  $logMonitor
     */
    public function __construct(LogMonitorContract $logMonitor)
    {
        parent::__construct();

        $this->logMonitor = $logMonitor;
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Display LogMonitor Logo and Copyrights.
     */
    protected function displayLogMonitor()
    {
          // ASCII Art Header (Updated Logo)
        $this->comment('   __                   _                        ');
        $this->comment('  / /  ___   __ _/\   /(_) _____      _____ _ __ ');
        $this->comment(' / /  / _ \\ / _` \\ \\ / / |/ _ \\ \\ /\\ / / _ \\ \'__|');
        $this->comment('/ /__| (_) | (_| |\\ V /| |  __/\\ V  V /  __/ |   ');
        $this->comment('\\____/\\___/ \\__, | \\_/ |_|\\___| \\_/\\_/ \\___|_|   ');
        $this->comment('            |___/                                ');
        $this->line('');


        // Copyright
        $this->comment('Version '.$this->logMonitor->version().' - Updated by Venkat '.chr(169));
        $this->line('');
    }
}
