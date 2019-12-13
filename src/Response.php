<?php

declare(strict_types=1);

namespace Sql;

use Impiura\Error\FromString;
use Impiura\Result;
use Impiura\Result\Declined;
use \Throwable;

class Response
{
    private $connection;
    private $query;

    public function __construct(Connection $connection, Query $query)
    {
        $this->connection = $connection;
        $this->query = $query;
    }

    public function result(): Result
    {
        try {
            $dbh = $this->connection->open();
        } catch (Throwable $e) {
            return new Declined(new FromString($e->getMessage()));
        }

        $result = $this->query->result($dbh);

        return $result;
    }
}
