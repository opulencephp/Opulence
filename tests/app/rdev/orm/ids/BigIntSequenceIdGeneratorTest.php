<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the big integer sequence Id generator
 */
namespace RDev\ORM\Ids;
use RDev\Tests\Mocks as ModelMocks;
use RDev\Tests\Databases\SQL\Mocks as SQLMocks;

class BigIntSequenceIdGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests generating an Id
     */
    public function testGeneratingId()
    {
        $server = new SQLMocks\Server();
        $connection = new SQLMocks\Connection($server);
        $entity = new ModelMocks\User(-1, "foo");
        $idGenerator = new BigIntSequenceIdGenerator("foo");
        $this->assertSame("1", $idGenerator->generate($entity, $connection));
        $this->assertSame("2", $idGenerator->generate($entity, $connection));
    }
} 