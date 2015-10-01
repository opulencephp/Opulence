<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for view lexers to implement
 */
namespace Opulence\Views\Compilers\Fortune\Lexers;

use Opulence\Views\Compilers\Fortune\Lexers\Tokens\Token;
use Opulence\Views\IView;

interface ILexer
{
    /**
     * Lexes input text and returns a list of tokens
     *
     * @param IView $view The view to lex
     * @return Token[] The list of tokens
     */
    public function lex(IView $view);
}