<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the tag node
 */
namespace RDev\Console\Responses\Compilers\Nodes;

class TagNodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests checking if a root node is a tag
     */
    public function testIsTag()
    {
        $node = new TagNode("foo");
        $this->assertTrue($node->isTag());
    }
}