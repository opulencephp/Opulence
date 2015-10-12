<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the big integer sequence Id generator
 */
namespace Opulence\ORM\Ids;

use Opulence\Tests\Mocks\User;
use Opulence\Tests\Databases\Mocks\Connection;
use Opulence\Tests\Databases\Mocks\Server;

class BigIntSequenceIdGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests generating an Id
     */
    public function testGeneratingId()
    {
        $server = new Server();
        $connection = new Connection($server);
        $entity = new User(-1, "foo");
        $idGenerator = new BigIntSequenceIdGenerator("foo");
        $this->assertSame("1", $idGenerator->generate($entity, $connection));
        $this->assertSame("2", $idGenerator->generate($entity, $connection));
    }
} 