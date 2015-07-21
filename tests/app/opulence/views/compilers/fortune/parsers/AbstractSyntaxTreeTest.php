<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the view abstract syntax tree
 */
namespace Opulence\Views\Compilers\Fortune\Parsers;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\Node;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\RootNode;

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
     * Tests clearing the nodes
     */
    public function testClearingNodes()
    {
        $this->tree->getCurrentNode()->addChild($this->getMockForAbstractClass(Node::class));
        $this->tree->clearNodes();
        $this->assertInstanceOf(RootNode::class, $this->tree->getCurrentNode());
        $this->assertEquals([], $this->tree->getRootNode()->getChildren());
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
        /** @var Node $currentNode */
        $currentNode = $this->getMockForAbstractClass(Node::class, ["foo"]);
        $this->assertSame($currentNode, $this->tree->setCurrentNode($currentNode));
        $this->assertSame($currentNode, $this->tree->getCurrentNode());
    }
}