<?php

declare(strict_types=1);

namespace Sql\Connection;

interface Host
{
    public function value(): string;
}
