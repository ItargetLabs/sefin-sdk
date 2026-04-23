<?php

declare(strict_types=1);

namespace SefinSdk\Exception;

use Throwable;

final class TransportException extends SefinException
{
    public static function fromThrowable(Throwable $throwable): self
    {
        return new self($throwable->getMessage(), (int) $throwable->getCode(), $throwable);
    }
}
