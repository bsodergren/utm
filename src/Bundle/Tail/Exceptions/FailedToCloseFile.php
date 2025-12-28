<?php
declare(strict_types=1);
namespace UTM\Bundle\Tail\Exceptions;

use Exception;

final class FailedToCloseFile extends Exception
{
    public function __construct(string $absoluteFilePath)
    {
        parent::__construct("Could not close file: " . $absoluteFilePath);
    }
}