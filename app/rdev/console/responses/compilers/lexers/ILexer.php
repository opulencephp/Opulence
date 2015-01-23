<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the interface for response lexers to implement
 */
namespace RDev\Console\Responses\Compilers\Lexers;
use RDev\Console\Responses\Compilers\Tokens;

interface ILexer 
{
    /**
     * Lexes input text and returns a list of tokens
     *
     * @param string $text The text to lex
     * @return Tokens\Token[] The list of tokens
     */
    public function lex($text);
}