<?php

declare(strict_types=1);

namespace Venkatesanchinna\LogMonitor\Utilities;

use Venkatesanchinna\LogMonitor\Contracts\Utilities\Factory as FactoryContract;
use Venkatesanchinna\LogMonitor\Contracts\Utilities\Filesystem as FilesystemContract;
use Venkatesanchinna\LogMonitor\Contracts\Utilities\LogLevels as LogLevelsContract;
use Venkatesanchinna\LogMonitor\Entities\LogCollection;
use Venkatesanchinna\LogMonitor\Tables\StatsTable;

/**
 * Class     Factory
 *
 * @author   Venkatesan <venkatesangee@gmail.com>
 */
class Factory implements FactoryContract
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * The filesystem instance.
     *
     * @var \Venkatesanchinna\LogMonitor\Contracts\Utilities\Filesystem
     */
    protected $filesystem;

    /**
     * The log levels instance.
     *
     * @var \Venkatesanchinna\LogMonitor\Contracts\Utilities\LogLevels
     */
    private $levels;
    private $log_folder_name;
    private $log_file;
    private $is_dashboard;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Create a new instance.
     *
     * @param  \Venkatesanchinna\LogMonitor\Contracts\Utilities\Filesystem  $filesystem
     * @param  \Venkatesanchinna\LogMonitor\Contracts\Utilities\LogLevels   $levels
     */
    public function __construct(FilesystemContract $filesystem, LogLevelsContract $levels) {
        $this->setFilesystem($filesystem);
        $this->setLevels($levels);
    }

    /* -----------------------------------------------------------------
     |  Getter & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the filesystem instance.
     *
     * @return \Venkatesanchinna\LogMonitor\Contracts\Utilities\Filesystem
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * Set the filesystem instance.
     *
     * @param  \Venkatesanchinna\LogMonitor\Contracts\Utilities\Filesystem  $filesystem
     *
     * @return self
     */
    public function setFilesystem(FilesystemContract $filesystem)
    {
        $this->filesystem = $filesystem;

        return $this;
    }

    /**
     * Get the log levels instance.
     *
     * @return \Venkatesanchinna\LogMonitor\Contracts\Utilities\LogLevels
     */
    public function getLevels()
    {
        return $this->levels;
    }

    /**
     * Set the log levels instance.
     *
     * @param  \Venkatesanchinna\LogMonitor\Contracts\Utilities\LogLevels  $levels
     *
     * @return self
     */
    public function setLevels(LogLevelsContract $levels)
    {
        $this->levels = $levels;

        return $this;
    }

    /**
     * Set the log storage path.
     *
     * @param  string  $storagePath
     *
     * @return self
     */
    public function setPath($storagePath)
    {
        $this->filesystem->setPath($storagePath);

        return $this;
    }

    /**
     * Get the log pattern.
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->filesystem->getPattern();
    }

    /**
     * Set the log pattern.
     *
     * @param  string  $date
     * @param  string  $prefix
     * @param  string  $extension
     *
     * @return self
     */
    public function setPattern(
        $prefix    = FilesystemContract::PATTERN_PREFIX,
        $date      = FilesystemContract::PATTERN_DATE,
        $extension = FilesystemContract::PATTERN_EXTENSION
    ) {
        $this->filesystem->setPattern($prefix, $date, $extension);

        return $this;
    }

    /**
     * Get all logs.
     *
     * @return \Venkatesanchinna\LogMonitor\Entities\LogCollection
     */
    public function logs($date = false)
    {
        $log_file = @$date ? $date : @$this->log_file;

        return (new LogCollection(null, $this->log_folder_name, $log_file, @$this->is_dashboard))->setFilesystem($this->filesystem);
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get all logs (alias).
     *
     * @see logs
     *
     * @return \Venkatesanchinna\LogMonitor\Entities\LogCollection
     */
    public function all()
    {
        return $this->logs();
    }

    /**
     * Paginate all logs.
     *
     * @param  int  $perPage
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = 30, $date = false)
    {
        return $this->logs($date)->paginate($perPage);
    }

    /**
     * Get a log by date.
     *
     * @param  string  $date
     *
     * @return \Venkatesanchinna\LogMonitor\Entities\Log
     */
    public function log($date)
    {
        return $this->logs($date)->log($date);
    }

    /**
     * Get a log by date (alias).
     *
     * @param  string  $date
     *
     * @return \Venkatesanchinna\LogMonitor\Entities\Log
     */
    public function get($date)
    {
        return $this->log($date);
    }

    /**
     * Get log entries.
     *
     * @param  string  $date
     * @param  string  $level
     *
     * @return \Venkatesanchinna\LogMonitor\Entities\LogEntryCollection
     */
    public function entries($date, $level = 'all')
    {
        $this->log_file = $date;
        return $this->logs($date)->entries($date, $level);
    }

    /**
     * Get logs statistics.
     *
     * @return array
     */
    public function stats()
    {
        return $this->logs()->stats();
    }

    /**
     * Get logs statistics table.
     *
     * @param  string|null  $locale
     *
     * @return \Venkatesanchinna\LogMonitor\Tables\StatsTable
     */
    public function statsTable($locale = null, $log_folder_name = false, $is_dashboard = false)
    {
        $this->log_folder_name = $log_folder_name;
        $this->is_dashboard = $is_dashboard;
        return StatsTable::make($this->stats($log_folder_name), $this->levels, $locale);
    }

    /**
     * List the log files (dates).
     *
     * @return array
     */
    public function dates()
    {
        return $this->logs()->dates();
    }

    /**
     * Get logs count.
     *
     * @return int
     */
    public function count()
    {
        return $this->logs()->count();
    }

    /**
     * Get total log entries.
     *
     * @param  string  $level
     *
     * @return int
     */
    public function total($level = 'all')
    {
        return $this->logs()->total($level);
    }

    /**
     * Get tree menu.
     *
     * @param  bool  $trans
     *
     * @return array
     */
    public function tree($trans = false)
    {
        return $this->logs()->tree($trans);
    }

    /**
     * Get tree menu.
     *
     * @param  bool  $trans
     *
     * @return array
     */
    public function menu($trans = true)
    {
        return $this->logs()->menu($trans);
    }

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */

    /**
     * Determine if the log folder is empty or not.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return $this->logs()->isEmpty();
    }
}
