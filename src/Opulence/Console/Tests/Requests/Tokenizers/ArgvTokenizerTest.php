<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Console\Tests\Requests\Tokenizers;

use Opulence\Console\Requests\Tokenizers\ArgvTokenizer;

/**
 * Tests the argv tokenizer
 */
class ArgvTokenizerTest extends \PHPUnit\Framework\TestCase
{
    /** @var ArgvTokenizer The tokenizer to use in tests */
    private $tokenizer;

    protected function setUp(): void
    {
        $this->tokenizer = new ArgvTokenizer();
    }

    public function testTokenizingEscapedDoubleQuote(): void
    {
        $tokens = $this->tokenizer->tokenize(['foo', 'Dave\"s']);
        $this->assertEquals(['Dave"s'], $tokens);
    }

    public function testTokenizingEscapedSingleQuote(): void
    {
        $tokens = $this->tokenizer->tokenize(['foo', "Dave\'s"]);
        $this->assertEquals(["Dave's"], $tokens);
    }
}
