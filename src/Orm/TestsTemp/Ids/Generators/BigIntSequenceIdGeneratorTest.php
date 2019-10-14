<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Orm\TestsTemp\Ids\Generators;

use Opulence\Databases\TestsTemp\Mocks\Connection;
use Opulence\Databases\TestsTemp\Mocks\Server;
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
    public function testCheckingIfPostInsert(): void
    {
        $generator = new BigIntSequenceIdGenerator('foo');
        $this->assertTrue($generator->isPostInsert());
    }

    public function testExceptionThrownWhenConnectionNotSet(): void
    {
        $this->expectException(OrmException::class);
        $idGenerator = new BigIntSequenceIdGenerator('foo');
        $idGenerator->generate(new stdClass());
    }

    public function testGeneratingId(): void
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
    public function testGettingEmptyValue(): void
    {
        $generator = new BigIntSequenceIdGenerator('foo');
        $this->assertNull($generator->getEmptyValue(new stdClass()));
    }

    public function testSettingNewConnectionOverridesConnectionInConstructor(): void
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
