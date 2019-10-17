<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Orm\Tests\Ids\Generators;

use Opulence\Databases\Tests\Mocks\Connection;
use Opulence\Databases\Tests\Mocks\Server;
use Opulence\Orm\Ids\Generators\IntSequenceIdGenerator;
use Opulence\Orm\OrmException;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Tests the integer sequence Id generator
 */
class IntSequenceIdGeneratorTest extends TestCase
{
    /**
     * Test checking if this generator is post-insert
     */
    public function testCheckingIfPostInsert(): void
    {
        $generator = new IntSequenceIdGenerator('foo');
        $this->assertTrue($generator->isPostInsert());
    }

    public function testExceptionThrownWhenConnectionNotSet(): void
    {
        $this->expectException(OrmException::class);
        $idGenerator = new IntSequenceIdGenerator('foo');
        $idGenerator->generate(new stdClass());
    }

    public function testGeneratingId(): void
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
    public function testGettingEmptyValue(): void
    {
        $generator = new IntSequenceIdGenerator('foo');
        $this->assertNull($generator->getEmptyValue(new stdClass()));
    }

    public function testSettingNewConnectionOverridesConnectionInConstructor(): void
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
