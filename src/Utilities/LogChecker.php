<?php

declare (strict_types = 1);

namespace Venkatesanchinna\LogMonitor\Utilities;

use Illuminate\Contracts\Config\Repository as ConfigContract;
use Venkatesanchinna\LogMonitor\Contracts\Utilities\Filesystem as FilesystemContract;
use Venkatesanchinna\LogMonitor\Contracts\Utilities\LogChecker as LogCheckerContract;

/**
 * Class     LogChecker
 *
 * @author   Venkatesan <venkatesangee@gmail.com>
 */
class LogChecker implements LogCheckerContract
{
    /* -----------------------------------------------------------------
    |  Properties
    | -----------------------------------------------------------------
     */

    /**
     * The config repository instance.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    private $config;

    /**
     * The filesystem instance.
     *
     * @var \Venkatesanchinna\LogMonitor\Contracts\Utilities\Filesystem
     */
    private $filesystem;

    /**
     * Log handler mode.
     *
     * @var string
     */
    protected $handler = '';

    /**
     * The check status.
     *
     * @var bool
     */
    private $status = true;

    /**
     * The check messages.
     *
     * @var array
     */
    private $messages;

    /**
     * Log files statuses.
     *
     * @var array
     */
    private $files = [];

    /* -----------------------------------------------------------------
    |  Constructor
    | -----------------------------------------------------------------
     */

    /**
     * LogChecker constructor.
     *
     * @param  \Illuminate\Contracts\Config\Repository              $config
     * @param  \Venkatesanchinna\LogMonitor\Contracts\Utilities\Filesystem  $filesystem
     */
    public function __construct(ConfigContract $config, FilesystemContract $filesystem)
    {
        $this->setConfig($config);
        $this->setFilesystem($filesystem);
        $this->refresh();
    }

    /* -----------------------------------------------------------------
    |  Getters & Setters
    | -----------------------------------------------------------------
     */

    /**
     * Set the config instance.
     *
     * @param  \Illuminate\Contracts\Config\Repository  $config
     *
     * @return self
     */
    public function setConfig(ConfigContract $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Set the Filesystem instance.
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
     * Set the log handler mode.
     *
     * @param  string  $handler
     *
     * @return self
     */
    protected function setHandler($handler)
    {
        $this->handler = strtolower($handler);

        return $this;
    }

    /* -----------------------------------------------------------------
    |  Main Methods
    | -----------------------------------------------------------------
     */

    /**
     * Get messages.
     *
     * @return array
     */
    public function messages()
    {
        $this->refresh();

        return $this->messages;
    }

    /**
     * Check if the checker passes.
     *
     * @return bool
     */
    public function passes()
    {
        $this->refresh();

        return $this->status;
    }

    /**
     * Check if the checker fails.
     *
     * @return bool
     */
    public function fails()
    {
        return !$this->passes();
    }

    /**
     * Get the requirements.
     *
     * @return array
     */
    public function requirements()
    {
        $this->refresh();

        return $this->isDaily() ? [
            'status'  => 'success',
            'header'  => 'Application requirements fulfilled.',
            'message' => 'Are you ready to rock ?',
        ] : [
            'status'  => 'failed',
            'header'  => 'Application requirements failed.',
            'message' => $this->messages['handler'],
        ];
    }

    /* -----------------------------------------------------------------
    |  Check Methods
    | -----------------------------------------------------------------
     */

    /**
     * Is a daily handler mode ?
     *
     * @return bool
     */
    protected function isDaily()
    {
        return $this->isSameHandler(self::HANDLER_DAILY);
    }

    /**
     * Is the handler is the same as the application log handler.
     *
     * @param  string  $handler
     *
     * @return bool
     */
    private function isSameHandler($handler)
    {
        return $this->handler === $handler;
    }

    /* -----------------------------------------------------------------
    |  Other Methods
    | -----------------------------------------------------------------
     */

    /**
     * Refresh the checks.
     *
     * @return \Venkatesanchinna\LogMonitor\Utilities\LogChecker
     */
    private function refresh()
    {
        $this->setHandler($this->config->get('logging.default', 'stack'));

        $this->messages = [
            'handler' => '',
            'files'   => [],
        ];
        $this->files = [];

        $this->checkHandler();
        $this->checkLogFiles();

        return $this;
    }

    /**
     * Check the handler mode.
     */
    private function checkHandler()
    {
        if ($this->isDaily()) {
            return;
        }

        $this->messages['handler'] = 'You should set the log handler to `daily` mode. Please check the LogViewer wiki page (Requirements) for more details.';
    }

    /**
     * Check all log files.
     */
    private function checkLogFiles()
    {
        foreach ($this->filesystem->all() as $path) {
            $this->checkLogFile($path);
        }
    }

    /**
     * Check a log file.
     *
     * @param  string  $path
     */
    private function checkLogFile($path)
    {
        $status   = true;
        $filename = basename($path);
        $message  = "The log file [$filename] is valid.";
        $pattern  = $this->filesystem->getPattern();

        $request_log_pattern = '^request-\d{4}-\d{2}-\d{2}\.log$';
        $custom_pattern      = '^.*\.log$';

        if ($this->isSingleLogFile($filename)) {
            $this->status                       = $status                       = false;
            $this->messages['files'][$filename] = $message =
                "You have a single log file in your application, you should split the [$filename] into separate log files.";
        } elseif ($this->isInvalidLogPattern($filename, $pattern) && $this->isInvalidLogPattern($filename, $request_log_pattern) && $this->isInvalidLogPattern($filename, $custom_pattern)) {
            $this->status                       = $status                       = false;
            $this->messages['files'][$filename] = $message =
                "The log file [$filename] has an invalid date, the format must be like {$pattern}.";
        }

        $this->files[$filename] = compact('filename', 'status', 'message', 'path');
    }

    /**
     * Check if it's not a single log file.
     *
     * @param  string  $file
     *
     * @return bool
     */
    private function isSingleLogFile($file)
    {
        return $file === 'laravel.log';
    }

    /**
     * Check the date of the log file.
     *
     * @param  string  $file
     * @param  string  $pattern
     *
     * @return bool
     */
    private function isInvalidLogPattern($file, $pattern)
    {
        return ((bool) preg_match("/{$pattern}/", $file, $matches)) === false;
    }
}
