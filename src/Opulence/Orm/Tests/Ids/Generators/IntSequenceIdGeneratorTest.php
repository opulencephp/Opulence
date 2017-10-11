<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Orm\Tests\Ids\Generators;

use Opulence\Databases\Tests\Mocks\Connection;
use Opulence\Databases\Tests\Mocks\Server;
use Opulence\Orm\Ids\Generators\IntSequenceIdGenerator;
use Opulence\Orm\OrmException;
use stdClass;

/**
 * Tests the integer sequence Id generator
 */
class IntSequenceIdGeneratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test checking if this generator is post-insert
     */
    public function testCheckingIfPostInsert()
    {
        $generator = new IntSequenceIdGenerator('foo');
        $this->assertTrue($generator->isPostInsert());
    }

    /**
     * Tests that an exception is thrown when no connection is set
     */
    public function testExceptionThrownWhenConnectionNotSet()
    {
        $this->expectException(OrmException::class);
        $idGenerator = new IntSequenceIdGenerator('foo');
        $idGenerator->generate(new stdClass());
    }

    /**
     * Tests generating an Id
     */
    public function testGeneratingId()
    {
        $server = new Server();
        $connection = new Connection($server);
        $entity = new stdClass();
        $idGenerator = new IntSequenceIdGenerator('foo', $connection);
        $this->assertSame(1, $idGenerator->generate($entity));
        $this->assertSame(2, $idGenerator->generate($entity));
    }

    /**
     *
     * Test getting empty value
     */
    public function testGettingEmptyValue()
    {
        $generator = new IntSequenceIdGenerator('foo');
        $this->assertNull($generator->getEmptyValue(new stdClass()));
    }

    /**
     * Tests setting new connection overrides connection in constructor
     */
    public function testSettingNewConnectionOverridesConnectionInConstructor()
    {
        $server = new Server();
        $connection = new Connection($server);
        $entity = new stdClass();
        $idGenerator = new IntSequenceIdGenerator('foo', $connection);
        $this->assertSame(1, $idGenerator->generate($entity));
        $this->assertSame(2, $idGenerator->generate($entity));
        $idGenerator->setConnection(new Connection($server));
        $this->assertSame(1, $idGenerator->generate($entity));
        $this->assertSame(2, $idGenerator->generate($entity));
    }
}
