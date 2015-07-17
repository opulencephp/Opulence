<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the root node
 */
namespace Opulence\Views\Compilers\Parsers\Nodes;

class RootNodeTest extends \PHPUnit_Framework_TestCase
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
        $this->assertFalse($node->isDirective());
        $this->assertFalse($node->isDirectiveName());
        $this->assertFalse($node->isExpression());
        $this->assertFalse($node->isSanitizedTag());
        $this->assertFalse($node->isUnsanitizedTag());
    }
}