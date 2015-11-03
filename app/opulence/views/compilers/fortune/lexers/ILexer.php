<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Views\Compilers\Fortune\Lexers;

use Opulence\Views\Compilers\Fortune\Lexers\Tokens\Token;
use Opulence\Views\IView;

/**
 * Defines the interface for view lexers to implement
 */
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