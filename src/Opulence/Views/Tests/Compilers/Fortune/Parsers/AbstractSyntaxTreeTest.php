<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Views\Tests\Compilers\Fortune\Parsers;

use Opulence\Views\Compilers\Fortune\Parsers\AbstractSyntaxTree;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\Node;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\RootNode;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the view abstract syntax tree
 */
class AbstractSyntaxTreeTest extends \PHPUnit\Framework\TestCase
{
    /** @var AbstractSyntaxTree The tree to use in tests */
    private $tree = null;

    /**
     * Sets up the tests
     */
    protected function setUp() : void
    {
        $this->tree = new AbstractSyntaxTree();
    }

    /**
     * Tests clearing the nodes
     */
    public function testClearingNodes() : void
    {
        /** @var Node|MockObject $childNode */
        $childNode = $this->getMockForAbstractClass(Node::class);
        $this->tree->getCurrentNode()->addChild($childNode);
        $this->tree->clearNodes();
        $this->assertInstanceOf(RootNode::class, $this->tree->getCurrentNode());
        $this->assertEquals([], $this->tree->getRootNode()->getChildren());
    }

    /**
     * Tests getting the current node when none is set
     */
    public function testGettingCurrentNodeWhenNoneIsSet() : void
    {
        $this->assertEquals(new RootNode(), $this->tree->getCurrentNode());
    }

    /**
     * Tests getting the root node
     */
    public function testGettingRootNode() : void
    {
        $this->assertEquals(new RootNode(), $this->tree->getRootNode());
    }

    /**
     * Tests setting the current node
     */
    public function testSettingCurrentNode() : void
    {
        /** @var Node $currentNode */
        $currentNode = $this->getMockForAbstractClass(Node::class, ['foo']);
        $this->assertSame($currentNode, $this->tree->setCurrentNode($currentNode));
        $this->assertSame($currentNode, $this->tree->getCurrentNode());
    }
}
