<?php

declare(strict_types=1);

namespace Sql\Tests\Connection;

use PDO;
use Sql\Connection;

class TestConnection implements Connection
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = null;
    }

    public function open(): PDO
    {
        if (is_null($this->pdo)) {
            $this->pdo = new PDO('sqlite::memory:');
        }

        return $this->pdo;
    }

    public function close(): void
    {
        $this->pdo = null;
    }
}