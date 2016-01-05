<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Console\Requests\Tokenizers;

/**
 * Defines the interface for tokenizers to implement
 */
interface ITokenizer
{
    /**
     * Tokenizes a request string
     *
     * @param mixed $input The input to tokenize
     * @return array The list of tokens
     */
    public function tokenize($input);
}