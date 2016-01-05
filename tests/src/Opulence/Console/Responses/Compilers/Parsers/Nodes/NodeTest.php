<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Console\Responses\Compilers\Parsers\Nodes;

use Opulence\Tests\Console\Responses\Compilers\Parsers\Nodes\Mocks\Node;

/**
 * Tests the response node
 */
class NodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests adding a child
     */
    public function testAddingChild()
    {
        $parent = new Node("foo");
        $child = new Node("bar");
        $parent->addChild($child);
        $this->assertEquals([$child], $parent->getChildren());
        $this->assertSame($parent, $child->getParent());
    }

    /**
     * Tests checking if nodes are leaves
     */
    public function testCheckingIfLeaves()
    {
        $parent = new Node("foo");
        $child = new Node("bar");
        $this->assertSame($parent, $parent->addChild($child));
        $this->assertFalse($parent->isLeaf());
        $this->assertTrue($child->isLeaf());
    }

    /**
     * Tests checking if nodes are roots
     */
    public function testCheckingIfRoots()
    {
        $parent = new Node("foo");
        $child = new Node("bar");
        $parent->addChild($child);
        $this->assertTrue($parent->isRoot());
        $this->assertFalse($child->isRoot());
    }

    /**
     * Tests getting the value
     */
    public function testGettingValue()
    {
        $node = new Node("foo");
        $this->assertEquals("foo", $node->getValue());
    }
}