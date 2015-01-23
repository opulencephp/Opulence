<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the word node
 */
namespace RDev\Console\Responses\Compilers\Parsers\Nodes;

class WordNodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests checking if a root node is a tag
     */
    public function testIsTag()
    {
        $node = new WordNode("foo");
        $this->assertFalse($node->isTag());
    }
}