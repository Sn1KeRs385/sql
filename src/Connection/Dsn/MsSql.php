<?php

declare(strict_types=1);

namespace Sql\Connection\Dsn;

use Sql\Connection\Dsn;
use Sql\Connection\Host;
use Sql\Connection\Port;

class MsSql implements Dsn
{
    private $host;
    private $port;

    public function __construct(Host $host, Port $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    public function value(): string
    {
        return sprintf('sqlsrv:server=%s,%s', $this->host->value(), $this->port->value());
    }
}