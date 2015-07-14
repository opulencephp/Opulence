<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the response node
 */
namespace RDev\Console\Responses\Compilers\Parsers\Nodes;
use RDev\Tests\Console\Responses\Compilers\Parsers\Nodes\Mocks\Node;

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