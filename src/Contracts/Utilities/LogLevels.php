<?php

declare(strict_types=1);

namespace Venkatesanchinna\LogMonitor\Contracts\Utilities;

use Illuminate\Translation\Translator;

/**
 * Interface  LogLevels
 *
 * @author    Venkatesan <venkatesangee@gmail.com>
 */
interface LogLevels
{
    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Set the Translator instance.
     *
     * @param  \Illuminate\Translation\Translator  $translator
     *
     * @return self
     */
    public function setTranslator(Translator $translator);

    /**
     * Get the selected locale.
     *
     * @return string
     */
    public function getLocale();

    /**
     * Set the selected locale.
     *
     * @param  string  $locale
     *
     * @return self
     */
    public function setLocale($locale);

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the log levels.
     *
     * @param  bool  $flip
     *
     * @return array
     */
    public function lists($flip = false);

    /**
     * Get translated levels.
     *
     * @param  string|null  $locale
     *
     * @return array
     */
    public function names($locale = null);

    /**
     * Get PSR log levels.
     *
     * @param  bool  $flip
     *
     * @return array
     */
    public static function all($flip = false);

    /**
     * Get the translated level.
     *
     * @param  string       $key
     * @param  string|null  $locale
     *
     * @return string
     */
    public function get($key, $locale = null);
}
