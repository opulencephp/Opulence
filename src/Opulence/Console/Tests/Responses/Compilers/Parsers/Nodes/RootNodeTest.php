<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Console\Tests\Responses\Compilers\Parsers\Nodes;

use Opulence\Console\Responses\Compilers\Parsers\Nodes\RootNode;

/**
 * Tests the root node
 */
class RootNodeTest extends \PHPUnit\Framework\TestCase
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
