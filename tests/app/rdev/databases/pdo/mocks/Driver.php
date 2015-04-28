<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks the PDO driver for use in testing
 */
namespace RDev\Tests\Databases\SQL\PDO\Mocks;
use RDev\Databases\Server;
use RDev\Databases\PDO\Driver as BaseDriver;
use RDev\Databases\Providers\Provider;

class Driver extends BaseDriver
{
    /**
     * {@inheritdoc}
     */
    protected function getDSN(Server $server, array $options = [])
    {
        return "fakedsn";
    }

    /**
     * {@inheritdoc}
     */
    protected function setProvider()
    {
        $this->provider = new Provider();
    }
} 