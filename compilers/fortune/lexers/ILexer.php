<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for view lexers to implement
 */
namespace Opulence\Views\Compilers\Fortune\Lexers;
use Opulence\Views\Compilers\Fortune\Lexers\Tokens\Token;
use Opulence\Views\IFortuneView;

interface ILexer
{
    /**
     * Lexes input text and returns a list of tokens
     *
     * @param IFortuneView $view The view to lex
     * @param string $content The text to lex
     * @return Token[] The list of tokens
     */
    public function lex(IFortuneView $view, $content);
}