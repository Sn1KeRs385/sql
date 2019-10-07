<?php

declare(strict_types=1);

namespace Sql\Connection;

interface DatabaseName
{
    public function value(): string;

    public function isSpecified(): bool;
}
