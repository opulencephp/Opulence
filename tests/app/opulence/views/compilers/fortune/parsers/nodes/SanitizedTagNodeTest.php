<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the sanitized tag name node
 */
namespace Opulence\Views\Compilers\Fortune\Parsers\Nodes;

class SanitizedTagNodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the "is a" methods
     */
    public function testIsAMethods()
    {
        $node = new SanitizedTagNode();
        $this->assertFalse($node->isDirective());
        $this->assertFalse($node->isDirectiveName());
        $this->assertFalse($node->isExpression());
        $this->assertTrue($node->isSanitizedTag());
        $this->assertFalse($node->isUnsanitizedTag());
    }
}