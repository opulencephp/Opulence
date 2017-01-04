<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Console\Responses\Compilers\Lexers;

use Opulence\Console\Responses\Compilers\Lexers\Tokens\Token;
use RuntimeException;

/**
 * Defines the interface for response lexers to implement
 */
interface ILexer
{
    /**
     * Lexes input text and returns a list of tokens
     *
     * @param string $text The text to lex
     * @return Token[] The list of tokens
     * @throws RuntimeException Thrown if there was an error lexing the text
     */
    public function lex(string $text) : array;
}
