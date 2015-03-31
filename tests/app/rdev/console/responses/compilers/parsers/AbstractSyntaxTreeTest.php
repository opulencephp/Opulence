<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the response abstract syntax tree
 */
namespace RDev\Console\Responses\Compilers\Parsers;
use RDev\Console\Responses\Compilers\Parsers\Nodes\RootNode;
use RDev\Tests\Console\Responses\Compilers\Parsers\Nodes\Mocks\Node;

class AbstractSyntaxTreeTest extends \PHPUnit_Framework_TestCase 
{
    /** @var AbstractSyntaxTree The tree to use in tests */
    private $tree = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->tree = new AbstractSyntaxTree();
    }

    /**
     * Tests getting the current node when none is set
     */
    public function testGettingCurrentNodeWhenNoneIsSet()
    {
        $this->assertEquals(new RootNode(), $this->tree->getCurrentNode());
    }

    /**
     * Tests getting the root node
     */
    public function testGettingRootNode()
    {
        $this->assertEquals(new RootNode(), $this->tree->getRootNode());
    }

    /**
     * Tests setting the current node
     */
    public function testSettingCurrentNode()
    {
        $currentNode = new Node("foo");
        $this->tree->setCurrentNode($currentNode);
        $this->assertSame($currentNode, $this->tree->getCurrentNode());
    }
}