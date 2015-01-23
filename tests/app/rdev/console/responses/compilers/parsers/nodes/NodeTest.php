<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the response node
 */
namespace RDev\Console\Responses\Compilers\Parsers\Nodes;
use RDev\Tests\Console\Responses\Compilers\Parsers\Nodes\Mocks;

class NodeTest extends \PHPUnit_Framework_TestCase 
{
    /**
     * Tests adding a child
     */
    public function testAddingChild()
    {
        $parent = new Mocks\Node("foo");
        $child = new Mocks\Node("bar");
        $parent->addChild($child);
        $this->assertEquals([$child], $parent->getChildren());
        $this->assertSame($parent, $child->getParent());
    }

    /**
     * Tests checking if nodes are leaves
     */
    public function testCheckingIfLeaves()
    {
        $parent = new Mocks\Node("foo");
        $child = new Mocks\Node("bar");
        $parent->addChild($child);
        $this->assertFalse($parent->isLeaf());
        $this->assertTrue($child->isLeaf());
    }

    /**
     * Tests checking if nodes are roots
     */
    public function testCheckingIfRoots()
    {
        $parent = new Mocks\Node("foo");
        $child = new Mocks\Node("bar");
        $parent->addChild($child);
        $this->assertTrue($parent->isRoot());
        $this->assertFalse($child->isRoot());
    }

    /**
     * Tests getting the value
     */
    public function testGettingValue()
    {
        $node = new Mocks\Node("foo");
        $this->assertEquals("foo", $node->getValue());
    }
}