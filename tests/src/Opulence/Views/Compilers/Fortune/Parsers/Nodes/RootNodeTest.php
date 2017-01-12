<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Views\Compilers\Fortune\Parsers\Nodes;

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
     * Tests the "is a" methods
     */
    public function testIsAMethods()
    {
        $node = new RootNode();
        $this->assertTrue($node->isRoot());
        $this->assertFalse($node->isComment());
        $this->assertFalse($node->isDirective());
        $this->assertFalse($node->isDirectiveName());
        $this->assertFalse($node->isExpression());
        $this->assertFalse($node->isSanitizedTag());
        $this->assertFalse($node->isUnsanitizedTag());
    }
}
