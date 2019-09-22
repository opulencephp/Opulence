<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

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
    private AbstractSyntaxTree $tree;

    protected function setUp(): void
    {
        $this->tree = new AbstractSyntaxTree();
    }

    public function testClearingNodes(): void
    {
        /** @var Node|MockObject $childNode */
        $childNode = $this->getMockForAbstractClass(Node::class);
        $this->tree->getCurrentNode()->addChild($childNode);
        $this->tree->clearNodes();
        $this->assertInstanceOf(RootNode::class, $this->tree->getCurrentNode());
        $this->assertEquals([], $this->tree->getRootNode()->getChildren());
    }

    public function testGettingCurrentNodeWhenNoneIsSet(): void
    {
        $this->assertEquals(new RootNode(), $this->tree->getCurrentNode());
    }

    public function testGettingRootNode(): void
    {
        $this->assertEquals(new RootNode(), $this->tree->getRootNode());
    }

    public function testSettingCurrentNode(): void
    {
        /** @var Node $currentNode */
        $currentNode = $this->getMockForAbstractClass(Node::class, ['foo']);
        $this->assertSame($currentNode, $this->tree->setCurrentNode($currentNode));
        $this->assertSame($currentNode, $this->tree->getCurrentNode());
    }
}
