<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the integer sequence Id generator
 */
namespace RDev\Models\ORM\Ids;
use RDev\Tests\Models\Mocks as ModelMocks;
use RDev\Tests\Models\Databases\SQL\Mocks as SQLMocks;

class IntSequenceIdGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests generating an Id
     */
    public function testGeneratingId()
    {
        $server = new SQLMocks\Server();
        $connection = new SQLMocks\Connection($server);
        $entity = new ModelMocks\User(-1, "foo");
        $idGenerator = new IntSequenceIdGenerator("foo");
        $this->assertSame(1, $idGenerator->generate($entity, $connection));
        $this->assertSame(2, $idGenerator->generate($entity, $connection));
    }
} 