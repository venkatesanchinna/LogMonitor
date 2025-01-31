<?php

declare(strict_types=1);

namespace Venkatesanchinna\LogMonitor\Exceptions;

/**
 * Class     LogNotFoundException
 *
 * @author   Venkatesan C <venkatesangee@gmail.com>
 */
class LogNotFoundException extends LogMonitorException
{
    /**
     * Make the exception.
     *
     * @param  string  $date
     *
     * @return static
     */
    public static function make(string $date)
    {
        return new static("Log not found in this date [{$date}]");
    }
}
