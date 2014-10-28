<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the PDO driver for use in testing
 */
namespace RDev\Tests\Databases\SQL\PDO\Mocks;
use RDev\Databases\SQL;
use RDev\Databases\SQL\PDO;
use RDev\Databases\SQL\Providers;

class Driver extends PDO\Driver
{
    /**
     * {@inheritdoc}
     */
    protected function getDSN(SQL\Server $server, array $options = [])
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