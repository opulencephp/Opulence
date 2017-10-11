<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Views\Tests\Compilers\Fortune\Parsers\Nodes;

use Opulence\Views\Compilers\Fortune\Parsers\Nodes\DirectiveNode;

/**
 * Tests the directive node
 */
class DirectiveNodeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests the "is a" methods
     */
    public function testIsAMethods()
    {
        $node = new DirectiveNode();
        $this->assertFalse($node->isComment());
        $this->assertTrue($node->isDirective());
        $this->assertFalse($node->isDirectiveName());
        $this->assertFalse($node->isExpression());
        $this->assertFalse($node->isSanitizedTag());
        $this->assertFalse($node->isUnsanitizedTag());
    }
}
