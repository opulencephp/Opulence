<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Creates instances of PDO connections
 */
namespace RDev\Models\Databases\SQL\PDO;
use RDev\Models\Databases\SQL;
use RDev\Models\Databases\SQL\IConnection;

class RDevPDOConnectionFactory implements SQL\IConnectionFactory
{
    /**
     * Creates a database connection
     *
     * @param SQL\Server $server The server to connect to
     * @return RDevPDO The database connection
     */
    public function create(SQL\Server $server)
    {
        return new RDevPDO($server);
    }
} 