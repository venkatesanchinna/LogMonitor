<?php

declare(strict_types=1);

namespace Venkatesanchinna\LogMonitor\Tables;

use Venkatesanchinna\LogMonitor\Contracts\Table as TableContract;
use Venkatesanchinna\LogMonitor\Contracts\Utilities\LogLevels as LogLevelsContract;

/**
 * Class     AbstractTable
 *
 * @author   Venkatesan C <venkatesangee@gmail.com>
 */
abstract class AbstractTable implements TableContract
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var array  */
    private $header  = [];

    /** @var array  */
    private $rows    = [];

    /** @var array  */
    private $footer  = [];

    /** @var \Venkatesanchinna\LogMonitor\Contracts\Utilities\LogLevels */
    protected $levels;

    /** @var string|null */
    protected $locale;

    /** @var array */
    private $data = [];

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Create a table instance.
     *
     * @param  array                                               $data
     * @param  \Venkatesanchinna\LogMonitor\Contracts\Utilities\LogLevels  $levels
     * @param  string|null                                         $locale
     */
    public function __construct(array $data, LogLevelsContract $levels, $locale = null)
    {
        $this->setLevels($levels);
        $this->setLocale(is_null($locale) ? config('log-monitor.locale') : $locale);
        $this->setData($data);
        $this->init();
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Set LogLevels instance.
     *
     * @param  \Venkatesanchinna\LogMonitor\Contracts\Utilities\LogLevels  $levels
     *
     * @return $this
     */
    protected function setLevels(LogLevelsContract $levels)
    {
        $this->levels = $levels;

        return $this;
    }

    /**
     * Set table locale.
     *
     * @param  string|null  $locale
     *
     * @return $this
     */
    protected function setLocale($locale)
    {
        if (is_null($locale) || $locale === 'auto') {
            $locale = app()->getLocale();
        }

        $this->locale = $locale;

        return $this;
    }

    /**
     * Get table header.
     *
     * @return array
     */
    public function header()
    {
        return $this->header;
    }

    /**
     * Get table rows.
     *
     * @return array
     */
    public function rows()
    {
        return $this->rows;
    }

    /**
     * Get table footer.
     *
     * @return array
     */
    public function footer()
    {
        return $this->footer;
    }

    /**
     * Get raw data.
     *
     * @return array
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * Set table data.
     *
     * @param  array  $data
     *
     * @return $this
     */
    private function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Prepare the table.
     */
    private function init()
    {
        $this->header = $this->prepareHeader($this->data);
        $this->rows   = $this->prepareRows($this->data);
        $this->footer = $this->prepareFooter($this->data);
    }

    /**
     * Prepare table header.
     *
     * @param  array  $data
     *
     * @return array
     */
    abstract protected function prepareHeader(array $data);

    /**
     * Prepare table rows.
     *
     * @param  array  $data
     *
     * @return array
     */
    abstract protected function prepareRows(array $data);

    /**
     * Prepare table footer.
     *
     * @param  array  $data
     *
     * @return array
     */
    abstract protected function prepareFooter(array $data);

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get log level color.
     *
     * @param  string  $level
     *
     * @return string
     */
    protected function color($level)
    {
        return log_styler()->color($level);
    }
}
