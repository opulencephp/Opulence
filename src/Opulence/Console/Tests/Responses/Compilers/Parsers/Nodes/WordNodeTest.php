<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Console\Tests\Responses\Compilers\Parsers\Nodes;

use Opulence\Console\Responses\Compilers\Parsers\Nodes\WordNode;

/**
 * Tests the word node
 */
class WordNodeTest extends \PHPUnit\Framework\TestCase
{
    public function testIsTag(): void
    {
        $node = new WordNode('foo');
        $this->assertFalse($node->isTag());
    }
}
