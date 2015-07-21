<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the expression node
 */
namespace Opulence\Views\Compilers\Fortune\Parsers\Nodes;

class ExpressionNodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the "is a" methods
     */
    public function testIsAMethods()
    {
        $node = new ExpressionNode();
        $this->assertFalse($node->isDirective());
        $this->assertFalse($node->isDirectiveName());
        $this->assertTrue($node->isExpression());
        $this->assertFalse($node->isSanitizedTag());
        $this->assertFalse($node->isUnsanitizedTag());
    }
}