<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for tokenizers to implement
 */
namespace Opulence\Console\Requests\Tokenizers;

interface ITokenizer
{
    /**
     * Tokenizes a request string
     *
     * @param mixed $input The input to tokenize
     * @return array The list of tokens
     */
    public function tokenize($input);
}