<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the argv parser
 */
namespace RDev\Console\Requests\Parsers;
use RDev\Console\Requests\Tokenizers;

class Argv extends Parser
{
    /** @var Tokenizers\Argv The tokenizer to use */
    private $tokenizer = null;

    public function __construct()
    {
        $this->tokenizer = new Tokenizers\Argv();
    }

    /**
     * {@inheritdoc}
     */
    public function parse($input)
    {
        if($input === null)
        {
            $input = $_SERVER["argv"];
        }

        if(!is_array($input))
        {
            throw new \InvalidArgumentException("Argv parser only accepts arrays as input");
        }

        $tokens = $this->tokenizer->tokenize($input);

        return $this->parseTokens($tokens);
    }
}