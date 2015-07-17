<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for view lexers to implement
 */
namespace Opulence\Views\Compilers\Lexers;
use Opulence\Views\Compilers\Lexers\Tokens\Token;
use Opulence\Views\ITemplate;

interface ILexer
{
    /**
     * Lexes input text and returns a list of tokens
     *
     * @param ITemplate $template The template to lex
     * @param string $content The text to lex
     * @return Token[] The list of tokens
     */
    public function lex(ITemplate $template, $content);
}