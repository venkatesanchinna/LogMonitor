<?php

declare(strict_types=1);

namespace Venkatesanchinna\LogMonitor\Exceptions;

/**
 * Class     FilesystemException
 *
 * @author   Venkatesan C <venkatesangee@gmail.com>
 */
class FilesystemException extends LogMonitorException
{
    public static function cannotDeleteLog()
    {
        return new static('There was an error deleting the log.');
    }

    public static function invalidPath(string $path)
    {
        return new static("The log(s) could not be located at : $path");
    }
}
