<?php

declare(strict_types=1);

namespace Sql\Connection;

interface Port
{
    public function value(): string;

    public function isSpecified(): bool;
}
