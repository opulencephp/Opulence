<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the interface for response parsers to implement
 */
namespace RDev\Console\Responses\Compilers\Parsers;
use RuntimeException;
use RDev\Console\Responses\Compilers\Lexers\Tokens\Token;

interface IParser 
{
    /**
     * Parses tokens into an abstract syntax tree
     *
     * @param Token[] $tokens The list of tokens to parse
     * @return AbstractSyntaxTree The abstract syntax tree made from the tokens
     * @throws RuntimeException Thrown if there was an error in the tokens
     */
    public function parse(array $tokens);
}