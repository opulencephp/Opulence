<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Console\Requests\Tokenizers;

/**
 * Tests the argv tokenizer
 */
class ArgvTokenizerTest extends \PHPUnit_Framework_TestCase
{
    /** @var ArgvTokenizer The tokenizer to use in tests */
    private $tokenizer = null;

    /**
     * Sets up the tests
     */
    public function setUp()
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
        $tokens = $this->tokenizer->tokenize(["foo", "Dave\'s"]);
        $this->assertEquals(["Dave's"], $tokens);
    }
}