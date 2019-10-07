<?php

declare(strict_types=1);

namespace Sql;

use Impiura\Result;
use PDO;

interface Query
{
    public function result(PDO $pdo): Result;
}
