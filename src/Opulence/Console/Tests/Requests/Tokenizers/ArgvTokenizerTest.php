<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Console\Tests\Requests\Tokenizers;

use Opulence\Console\Requests\Tokenizers\ArgvTokenizer;

/**
 * Tests the argv tokenizer
 */
class ArgvTokenizerTest extends \PHPUnit\Framework\TestCase
{
    /** @var ArgvTokenizer The tokenizer to use in tests */
    private $tokenizer = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->tokenizer = new ArgvTokenizer();
    }

    /**
     * Tests tokenizing an escaped double quote
     */
    public function testTokenizingEscapedDoubleQuote()
    {
        $tokens = $this->tokenizer->tokenize(['foo', 'Dave\"s']);
        $this->assertEquals(['Dave"s'], $tokens);
    }

    /**
     * Tests tokenizing an escaped single quote
     */
    public function testTokenizingEscapedSingleQuote()
    {
        $tokens = $this->tokenizer->tokenize(['foo', "Dave\'s"]);
        $this->assertEquals(["Dave's"], $tokens);
    }
}
