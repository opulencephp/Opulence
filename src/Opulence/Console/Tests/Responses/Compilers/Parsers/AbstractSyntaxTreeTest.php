<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Console\Tests\Responses\Compilers\Parsers;

use Opulence\Console\Responses\Compilers\Parsers\AbstractSyntaxTree;
use Opulence\Console\Responses\Compilers\Parsers\Nodes\RootNode;
use Opulence\Console\Tests\Responses\Compilers\Parsers\Nodes\Mocks\Node;

/**
 * Tests the response abstract syntax tree
 */
class AbstractSyntaxTreeTest extends \PHPUnit\Framework\TestCase
{
    /** @var AbstractSyntaxTree The tree to use in tests */
    private $tree;

    protected function setUp(): void
    {
        $this->tree = new AbstractSyntaxTree();
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
        $currentNode = new Node('foo');
        $this->assertSame($currentNode, $this->tree->setCurrentNode($currentNode));
        $this->assertSame($currentNode, $this->tree->getCurrentNode());
    }
}
