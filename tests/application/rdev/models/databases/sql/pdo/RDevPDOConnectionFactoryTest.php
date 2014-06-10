<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the PDO connection factory
 */
namespace RDev\Models\Databases\SQL\PDO;
use RDev\Models\Databases\SQL;

class RDevPDOConnectionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests creating an instance
     */
    public function testCreatingInstance()
    {
        $factory = new RDevPDOConnectionFactory();
        $server = $this->getMockForAbstractClass("RDev\\Models\\Databases\\SQL\\Server");
        $connection = $factory->create($server);
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\PDO\\RDevPDO", $connection);
    }
} 