<?php

declare(strict_types=1);

namespace Venkatesanchinna\LogMonitor\Support\Exceptions;

use Exception;

/**
 * Class     PackageException
 *
 * @author   Venkatesan C <venkatesangee@gmail.com>
 */
class PackageException extends Exception
{
    public static function unspecifiedName(): self
    {
        return new static('You must specify the vendor/package name.');
    }
}
