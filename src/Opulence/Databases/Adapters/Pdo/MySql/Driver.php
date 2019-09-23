<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Databases\Adapters\Pdo\MySql;

use Opulence\Databases\Adapters\Pdo\Driver as BaseDriver;
use Opulence\Databases\Providers\MySqlProvider;
use Opulence\Databases\Server;

/**
 * Defines the PDO driver for a MySQL database
 */
final class Driver extends BaseDriver
{
    /**
     * @inheritdoc
     */
    protected function getDsn(Server $server, array $options = []): string
    {
        $dsn = implode(';', [
                'mysql:host=' . $server->getHost(),
                'dbname=' . $server->getDatabaseName(),
                'port=' . $server->getPort(),
                'charset=' . $server->getCharset()
            ]) . ';';

        if (isset($options['unix_socket'])) {
            $dsn .= 'unix_socket=' . $options['unix_socket'] . ';';
        }

        return $dsn;
    }

    /**
     * @inheritdoc
     */
    protected function setProvider(): void
    {
        $this->provider = new MySqlProvider();
    }
}
