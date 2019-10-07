<?php

declare(strict_types=1);

namespace Sql\Connection\Port;

use Sql\Connection\Port;

class SpecifiedPort implements Port
{
    private $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function value(): string
    {
        return sprintf('%s', $this->value);
    }

    public function isSpecified(): bool
    {
        return true;
    }
}
