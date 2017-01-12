<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Databases\Adapters\Pdo\PostgreSql;

use Opulence\Databases\Adapters\Pdo\Driver as BaseDriver;
use Opulence\Databases\Providers\PostgreSqlProvider;
use Opulence\Databases\Server;

/**
 * Defines the PDO driver for a PostgreSQL database
 */
class Driver extends BaseDriver
{
    /**
     * @inheritdoc
     */
    protected function getDsn(Server $server, array $options = []) : string
    {
        $dsn = implode(';', [
                'pgsql:host=' . $server->getHost(),
                'dbname=' . $server->getDatabaseName(),
                'port=' . $server->getPort(),
                "options='--client_encoding=" . $server->getCharset() . "'"
            ]) . ';';

        if (isset($options['sslmode'])) {
            $dsn .= 'sslmode=' . $options['sslmode'] . ';';
        }

        return $dsn;
    }

    /**
     * @inheritdoc
     */
    protected function setProvider()
    {
        $this->provider = new PostgreSqlProvider();
    }
}
