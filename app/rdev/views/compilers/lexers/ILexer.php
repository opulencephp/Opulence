<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for view lexers to implement
 */
namespace RDev\Views\Compilers\Lexers;
use RDev\Views\Compilers\Lexers\Tokens\Token;
use RDev\Views\ITemplate;

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