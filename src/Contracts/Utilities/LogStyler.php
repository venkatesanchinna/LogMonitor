<?php

declare(strict_types=1);

namespace Venkatesanchinna\LogMonitor\Contracts\Utilities;

/**
 * Interface  LogStyler
 *
 * @author    Venkatesan <venkatesangee@gmail.com>
 */
interface LogStyler
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Make level icon.
     *
     * @param  string       $level
     * @param  string|null  $default
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function icon($level, $default = null);

    /**
     * Get level color.
     *
     * @param  string       $level
     * @param  string|null  $default
     *
     * @return string
     */
    public function color($level, $default = null);

    /**
     * Get strings to highlight.
     *
     * @param  array  $default
     *
     * @return array
     */
    public function toHighlight(array $default = []);
}
