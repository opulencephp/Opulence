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

use Opulence\Orm\Ids\Generators\UuidV4Generator;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Tests the UUID V4 generator
 */
class UuidV4GeneratorTest extends TestCase
{
    /**
     * Test checking if this generator is post-insert
     */
    public function testCheckingIfPostInsert(): void
    {
        $generator = new UuidV4Generator();
        $this->assertFalse($generator->isPostInsert());
    }

    public function testGeneratingId(): void
    {
        $entity = new stdClass();
        $idGenerator = new UuidV4Generator();
        $id1 = $idGenerator->generate($entity);
        $id2 = $idGenerator->generate($entity);
        $this->assertNotSame($id1, $id2);
        $this->assertEquals(36, strlen($id1));
        $this->assertEquals(36, strlen($id2));
    }

    /**
     *
     * Test getting empty value
     */
    public function testGettingEmptyValue(): void
    {
        $generator = new UuidV4Generator();
        $this->assertSame('', $generator->getEmptyValue(new stdClass()));
    }
}
