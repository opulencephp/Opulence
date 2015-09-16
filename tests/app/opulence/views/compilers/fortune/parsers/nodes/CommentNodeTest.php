<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the comment node
 */
namespace Opulence\Views\Compilers\Fortune\Parsers\Nodes;

class CommentNodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the "is a" methods
     */
    public function testIsAMethods()
    {
        $node = new CommentNode();
        $this->assertTrue($node->isComment());
        $this->assertFalse($node->isDirective());
        $this->assertFalse($node->isDirectiveName());
        $this->assertFalse($node->isExpression());
        $this->assertFalse($node->isSanitizedTag());
        $this->assertFalse($node->isUnsanitizedTag());
    }
}