<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the integer sequence Id generator
 */
namespace Opulence\ORM\Ids;

use Opulence\Tests\Mocks\User;
use Opulence\Tests\Databases\SQL\Mocks\Connection;
use Opulence\Tests\Databases\SQL\Mocks\Server;

class IntSequenceIdGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests generating an Id
     */
    public function testGeneratingId()
    {
        $server = new Server();
        $connection = new Connection($server);
        $entity = new User(-1, "foo");
        $idGenerator = new IntSequenceIdGenerator("foo");
        $this->assertSame(1, $idGenerator->generate($entity, $connection));
        $this->assertSame(2, $idGenerator->generate($entity, $connection));
    }
} 