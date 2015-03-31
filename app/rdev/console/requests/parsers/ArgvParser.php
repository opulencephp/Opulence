<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the argv parser
 */
namespace RDev\Console\Requests\Parsers;
use InvalidArgumentException;
use RDev\Console\Requests\Tokenizers\ArgvTokenizer;

class ArgvParser extends Parser
{
    /** @var ArgvTokenizer The tokenizer to use */
    private $tokenizer = null;

    public function __construct()
    {
        $this->tokenizer = new ArgvTokenizer();
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
            throw new InvalidArgumentException("ArgvParser parser only accepts arrays as input");
        }

        $tokens = $this->tokenizer->tokenize($input);

        return $this->parseTokens($tokens);
    }
}