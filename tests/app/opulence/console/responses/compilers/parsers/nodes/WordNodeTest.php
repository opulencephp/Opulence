<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Console\Responses\Compilers\Parsers\Nodes;

/**
 * Tests the word node
 */
class WordNodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests checking if a root node is a tag
     */
    public function testIsTag()
    {
        $node = new WordNode("foo");
        $this->assertFalse($node->isTag());
    }
}