<?php

declare(strict_types=1);

namespace Sql\Connection;

interface Credentials
{
    public function username(): string;

    public function password(): string;
}
