<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Views\Tests\Compilers\Fortune\Parsers\Nodes;

use Opulence\Views\Compilers\Fortune\Parsers\Nodes\UnsanitizedTagNode;

/**
 * Tests the unsanitized tag node
 */
class UnsanitizedTagNodeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests the "is a" methods
     */
    public function testIsAMethods()
    {
        $node = new UnsanitizedTagNode();
        $this->assertFalse($node->isComment());
        $this->assertFalse($node->isDirective());
        $this->assertFalse($node->isDirectiveName());
        $this->assertFalse($node->isExpression());
        $this->assertFalse($node->isSanitizedTag());
        $this->assertTrue($node->isUnsanitizedTag());
    }
}
