<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the unsanitized tag node
 */
namespace Opulence\Views\Compilers\Fortune\Parsers\Nodes;

class UnsanitizedTagNodeTest extends \PHPUnit_Framework_TestCase
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