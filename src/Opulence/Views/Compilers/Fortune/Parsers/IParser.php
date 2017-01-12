<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Views\Compilers\Fortune\Parsers;

use Opulence\Views\Compilers\Fortune\Lexers\Tokens\Token;
use RuntimeException;

/**
 * Parses a stream of tokens into an abstract syntax tree
 */
interface IParser
{
    /**
     * Parses a list of tokens into an abstract syntax tree
     *
     * @param Token[] $tokens The list of tokens to parse
     * @return AbstractSyntaxTree The abstract syntax tree
     * @throws RuntimeException Thrown if the stream of tokens was invalid
     */
    public function parse(array $tokens) : AbstractSyntaxTree;
}
