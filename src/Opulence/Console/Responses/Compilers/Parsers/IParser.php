<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Console\Responses\Compilers\Parsers;

use Opulence\Console\Responses\Compilers\Lexers\Tokens\Token;
use RuntimeException;

/**
 * Defines the interface for response parsers to implement
 */
interface IParser
{
    /**
     * Parses tokens into an abstract syntax tree
     *
     * @param Token[] $tokens The list of tokens to parse
     * @return AbstractSyntaxTree The abstract syntax tree made from the tokens
     * @throws RuntimeException Thrown if there was an error in the tokens
     */
    public function parse(array $tokens) : AbstractSyntaxTree;
}
