<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the string parser
 */
namespace RDev\Console\Requests\Parsers;
use RDev\Console\Requests\Tokenizers\StringTokenizer;

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