<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the array list parser
 */
namespace Opulence\Console\Requests\Parsers;
use InvalidArgumentException;
use Opulence\Console\Requests\Tokenizers\ArrayListTokenizer;

class ArrayListParser extends Parser
{
    /** @var ArrayListTokenizer The tokenizer to use */
    private $tokenizer = null;

    public function __construct()
    {
        $this->tokenizer = new ArrayListTokenizer();
    }

    /**
     * @inheritdoc
     */
    public function parse($input)
    {
        if(!is_array($input))
        {
            throw new InvalidArgumentException(__METHOD__ . " only accepts arrays as input");
        }

        $tokens = $this->tokenizer->tokenize($input);

        return $this->parseTokens($tokens);
    }
}