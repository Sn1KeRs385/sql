<?php

declare(strict_types=1);

namespace Sql\Query;

use Impiura\Error\FromString;
use Impiura\Result;
use Impiura\Result\Declined;
use Impiura\Result\Successful;
use Impiura\Value\Emptie;
use PDO;
use Sql\Query;
use Throwable;

class Mutating implements Query
{
    private $queryString;
    private $values;

    public function __construct(string $queryString, array $values)
    {
        $this->queryString = $queryString;
        $this->values = $values;
    }

    public function result(PDO $pdo): Result
    {
        try {
            $statement = $pdo->prepare($this->queryString);
        } catch (Throwable $e) {
            return new Declined(new FromString($e->getMessage()));
        }

        try {
            $result = $statement->execute($this->values);
        } catch (Throwable $e) {
            return new Declined(new FromString($e->getMessage()));
        }

        if ($result === false) {
            return new Declined(new FromString($statement->errorInfo()[2]));
        }

        $statement->closeCursor();
        $statement = null;

        return new Successful(new Emptie());
    }
}
