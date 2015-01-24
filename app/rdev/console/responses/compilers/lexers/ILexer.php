<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the interface for response lexers to implement
 */
namespace RDev\Console\Responses\Compilers\Lexers;

interface ILexer 
{
    /**
     * Lexes input text and returns a list of tokens
     *
     * @param string $text The text to lex
     * @return Tokens\Token[] The list of tokens
     * @throws \RuntimeException Thrown if there was an error lexing the text
     */
    public function lex($text);
}