<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the root node
 */
namespace RDev\Console\Responses\Compilers\Parsers\Nodes;

class RootNodeTest extends \PHPUnit_Framework_TestCase 
{
    /**
     * Tests getting the parent
     */
    public function testGettingParent()
    {
        $node = new RootNode();
        $this->assertSame($node, $node->getParent());
    }

    /**
     * Tests checking if a root node is root
     */
    public function testIsRoot()
    {
        $node = new RootNode();
        $this->assertTrue($node->isRoot());
    }

    /**
     * Tests checking if a root node is a tag
     */
    public function testIsTag()
    {
        $node = new RootNode();
        $this->assertFalse($node->isTag());
    }
}