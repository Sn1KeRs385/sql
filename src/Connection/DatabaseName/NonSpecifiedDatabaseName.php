<?php

declare(strict_types=1);

namespace Sql\Connection\DatabaseName;

use Sql\Connection\DatabaseName;

class NonSpecifiedDatabaseName implements DatabaseName
{
    public function value(): string
    {
        return '';
    }

    public function isSpecified(): bool
    {
        return false;
    }
}
