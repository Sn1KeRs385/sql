<?php

declare(strict_types=1);


namespace Sql\Connection;


interface Dsn
{
    public function value(): string;
}