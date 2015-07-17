<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the string parser
 */
namespace Opulence\Console\Requests\Parsers;
use Opulence\Console\Requests\Tokenizers\StringTokenizer;

class StringParser extends Parser
{
    /** @var StringTokenizer The tokenizer to use */
    private $tokenizer = null;

    public function __construct()
    {
        $this->tokenizer = new StringTokenizer();
    }

    /**
     * {@inheritdoc}
     */
    public function parse($input)
    {
        $tokens = $this->tokenizer->tokenize($input);

        return $this->parseTokens($tokens);
    }
}