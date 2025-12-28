<?php
declare(strict_types=1);
namespace UTM\Bundle\Tail\Exceptions;

use Exception;

final class FailedToReadFile extends Exception
{
    public function __construct(string $absoluteFilePath, ?Exception $exception = null)
    {
        parent::__construct("Could not open file for reading: " . $absoluteFilePath, 0, $exception);
    }
}