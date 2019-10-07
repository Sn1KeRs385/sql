<?php

declare(strict_types=1);

namespace Sql;

use PDO;
use Exception;

interface Connection
{
    /**
     * @throws Exception
     */
    public function open(): PDO;

    public function close(): void;
}
