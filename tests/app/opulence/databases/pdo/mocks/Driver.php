<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks the PDO driver for use in testing
 */
namespace Opulence\Tests\Databases\PDO\Mocks;

use Opulence\Databases\PDO\Driver as BaseDriver;
use Opulence\Databases\Providers\Provider;
use Opulence\Databases\Server;

class Driver extends BaseDriver
{
    /**
     * @inheritdoc
     */
    protected function getDSN(Server $server, array $options = [])
    {
        return "fakedsn";
    }

    /**
     * @inheritdoc
     */
    protected function setProvider()
    {
        $this->provider = new Provider();
    }
} 