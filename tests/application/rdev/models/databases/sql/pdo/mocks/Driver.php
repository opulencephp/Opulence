<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the PDO driver for use in testing
 */
namespace RDev\Tests\Models\Databases\SQL\PDO\Mocks;
use RDev\Models\Databases\SQL;
use RDev\Models\Databases\SQL\PDO;
use RDev\Models\Databases\SQL\Providers;

class Driver extends PDO\Driver
{
    /**
     * {@inheritdoc}
     */
    protected function createDSN(SQL\Server $server, array $options = [])
    {
        return "fakedsn";
    }

    /**
     * {@inheritdoc}
     */
    protected function setProvider()
    {
        $this->provider = new Providers\Provider();
    }
} 