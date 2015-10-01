<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for response lexers to implement
 */
namespace Opulence\Console\Responses\Compilers\Lexers;

use Opulence\Console\Responses\Compilers\Lexers\Tokens\Token;
use RuntimeException;

interface ILexer
{
    /**
     * Lexes input text and returns a list of tokens
     *
     * @param string $text The text to lex
     * @return Token[] The list of tokens
     * @throws RuntimeException Thrown if there was an error lexing the text
     */
    public function lex($text);
}