<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Orm\Tests\Ids\Generators;

use Opulence\Databases\Tests\Mocks\Connection;
use Opulence\Databases\Tests\Mocks\Server;
use Opulence\Orm\Ids\Generators\BigIntSequenceIdGenerator;
use Opulence\Orm\OrmException;
use stdClass;

/**
 * Tests the big integer sequence Id generator
 */
class BigIntSequenceIdGeneratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test checking if this generator is post-insert
     */
    public function testCheckingIfPostInsert()
    {
        $generator = new BigIntSequenceIdGenerator('foo');
        $this->assertTrue($generator->isPostInsert());
    }

    /**
     * Tests that an exception is thrown when no connection is set
     */
    public function testExceptionThrownWhenConnectionNotSet()
    {
        $this->expectException(OrmException::class);
        $idGenerator = new BigIntSequenceIdGenerator('foo');
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
        $idGenerator = new BigIntSequenceIdGenerator('foo', $connection);
        $this->assertSame('1', $idGenerator->generate($entity));
        $this->assertSame('2', $idGenerator->generate($entity));
    }

    /**
     *
     * Test getting empty value
     */
    public function testGettingEmptyValue()
    {
        $generator = new BigIntSequenceIdGenerator('foo');
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
        $idGenerator = new BigIntSequenceIdGenerator('foo', $connection);
        $this->assertSame('1', $idGenerator->generate($entity));
        $this->assertSame('2', $idGenerator->generate($entity));
        $idGenerator->setConnection(new Connection($server));
        $this->assertSame('1', $idGenerator->generate($entity));
        $this->assertSame('2', $idGenerator->generate($entity));
    }
}
