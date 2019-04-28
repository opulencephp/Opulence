<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Views\Tests\Compilers\Fortune\Parsers\Nodes;

use Opulence\Views\Compilers\Fortune\Parsers\Nodes\Node;

/**
 * Tests the view node
 */
class NodeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests adding a child
     */
    public function testAddingChild(): void
    {
        /** @var Node $parent */
        $parent = $this->getMockForAbstractClass(Node::class, ['foo']);
        /** @var Node $child */
        $child = $this->getMockForAbstractClass(Node::class, ['bar']);
        $this->assertSame($parent, $parent->addChild($child));
        $this->assertEquals([$child], $parent->getChildren());
        $this->assertSame($parent, $child->getParent());
    }

    /**
     * Tests checking if nodes are leaves
     */
    public function testCheckingIfLeaves(): void
    {
        /** @var Node $parent */
        $parent = $this->getMockForAbstractClass(Node::class, ['foo']);
        /** @var Node $child */
        $child = $this->getMockForAbstractClass(Node::class, ['bar']);
        $parent->addChild($child);
        $this->assertFalse($parent->isLeaf());
        $this->assertTrue($child->isLeaf());
    }

    /**
     * Tests checking if nodes are roots
     */
    public function testCheckingIfRoots(): void
    {
        /** @var Node $parent */
        $parent = $this->getMockForAbstractClass(Node::class, ['foo']);
        /** @var Node $child */
        $child = $this->getMockForAbstractClass(Node::class, ['bar']);
        $parent->addChild($child);
        $this->assertTrue($parent->isRoot());
        $this->assertFalse($child->isRoot());
    }

    /**
     * Tests getting the value
     */
    public function testGettingValue(): void
    {
        /** @var Node $node */
        $node = $this->getMockForAbstractClass(Node::class, ['foo']);
        $this->assertEquals('foo', $node->getValue());
    }
}
