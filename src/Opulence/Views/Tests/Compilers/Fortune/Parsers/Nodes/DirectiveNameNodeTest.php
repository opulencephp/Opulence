<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Views\Tests\Compilers\Fortune\Parsers\Nodes;

use Opulence\Views\Compilers\Fortune\Parsers\Nodes\DirectiveNameNode;

/**
 * Tests the directive name node
 */
class DirectiveNameNodeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests the "is a" methods
     */
    public function testIsAMethods(): void
    {
        $node = new DirectiveNameNode();
        $this->assertFalse($node->isComment());
        $this->assertFalse($node->isDirective());
        $this->assertTrue($node->isDirectiveName());
        $this->assertFalse($node->isExpression());
        $this->assertFalse($node->isSanitizedTag());
        $this->assertFalse($node->isUnsanitizedTag());
    }
}
