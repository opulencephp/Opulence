<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Console\Tests\Requests\Tokenizers;

use Opulence\Console\Requests\Tokenizers\ArrayListTokenizer;
use RuntimeException;

/**
 * Tests the array list tokenizer
 */
class ArrayListTokenizerTest extends \PHPUnit\Framework\TestCase
{
    /** @var ArrayListTokenizer The tokenizer to use in tests */
    private $tokenizer = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->tokenizer = new ArrayListTokenizer();
    }

    /**
     * Test not passing the command name
     */
    public function testNotPassingCommandName()
    {
        $this->expectException(RuntimeException::class);
        $this->tokenizer->tokenize([
            'foo' => 'bar'
        ]);
    }

    /**
     * Tests tokenizing arguments and options
     */
    public function testTokenizingArgumentsAndOptions()
    {
        $tokens = $this->tokenizer->tokenize([
            'name' => 'foo',
            'arguments' => ['bar'],
            'options' => ['--name=dave', '-r']
        ]);
        $this->assertEquals(['foo', 'bar', '--name=dave', '-r'], $tokens);
    }
}
