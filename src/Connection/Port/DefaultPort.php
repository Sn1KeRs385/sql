<?php

declare(strict_types=1);

namespace Sql\Connection\Port;

use Exception;
use Sql\Connection\Port;

class DefaultPort implements Port
{
    public function value(): string
    {
        throw new Exception('Default port is used. If you want to specify a concrete port, use SpecifiedPort class instead.');
    }

    public function isSpecified(): bool
    {
        return false;
    }
}
