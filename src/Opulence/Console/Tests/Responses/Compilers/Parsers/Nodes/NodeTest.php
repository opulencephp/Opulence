<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Console\Tests\Responses\Compilers\Parsers\Nodes;

use Opulence\Console\Tests\Responses\Compilers\Parsers\Nodes\Mocks\Node;

/**
 * Tests the response node
 */
class NodeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests adding a child
     */
    public function testAddingChild(): void
    {
        $parent = new Node('foo');
        $child = new Node('bar');
        $parent->addChild($child);
        $this->assertEquals([$child], $parent->getChildren());
        $this->assertSame($parent, $child->getParent());
    }

    /**
     * Tests checking if nodes are leaves
     */
    public function testCheckingIfLeaves(): void
    {
        $parent = new Node('foo');
        $child = new Node('bar');
        $this->assertSame($parent, $parent->addChild($child));
        $this->assertFalse($parent->isLeaf());
        $this->assertTrue($child->isLeaf());
    }

    /**
     * Tests checking if nodes are roots
     */
    public function testCheckingIfRoots(): void
    {
        $parent = new Node('foo');
        $child = new Node('bar');
        $parent->addChild($child);
        $this->assertTrue($parent->isRoot());
        $this->assertFalse($child->isRoot());
    }

    /**
     * Tests getting the value
     */
    public function testGettingValue(): void
    {
        $node = new Node('foo');
        $this->assertEquals('foo', $node->getValue());
    }
}
