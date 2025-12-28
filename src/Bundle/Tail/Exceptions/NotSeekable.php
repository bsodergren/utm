<?php
declare(strict_types=1);
namespace UTM\Bundle\Tail\Exceptions;

use Exception;

final class NotSeekable extends Exception
{
    public function __construct(string $absoluteFilePath, Exception $exception)
    {
        parent::__construct("Not seekable: " . $absoluteFilePath, 0, $exception);
    }
}