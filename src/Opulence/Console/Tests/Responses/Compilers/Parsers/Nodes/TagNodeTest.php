<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Console\Tests\Responses\Compilers\Parsers\Nodes;

use Opulence\Console\Responses\Compilers\Parsers\Nodes\TagNode;

/**
 * Tests the tag node
 */
class TagNodeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests checking if a root node is a tag
     */
    public function testIsTag()
    {
        $node = new TagNode('foo');
        $this->assertTrue($node->isTag());
    }
}
