<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the string parser
 */
namespace RDev\Console\Requests\Parsers;
use RDev\Console\Requests\Tokenizers;

class String extends Parser
{
    /** @var Tokenizers\String The tokenizer to use */
    private $tokenizer = null;

    public function __construct()
    {
        $this->tokenizer = new Tokenizers\String();
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