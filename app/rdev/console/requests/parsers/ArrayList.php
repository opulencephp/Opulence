<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the array list parser
 */
namespace RDev\Console\Requests\Parsers;
use RDev\Console\Requests;

class ArrayList extends Parser
{
    /** @var Tokenizers\ArrayList The tokenizer to use */
    private $tokenizer = null;

    public function __construct()
    {
        $this->tokenizer = new Tokenizers\ArrayList();
    }

    /**
     * {@inheritdoc}
     */
    public function parse($input)
    {
        if(!is_array($input))
        {
            throw new \InvalidArgumentException("ArrayList parser only accepts arrays as input");
        }

        $tokens = $this->tokenizer->tokenize($input);

        return $this->parseTokens($tokens);
    }
}