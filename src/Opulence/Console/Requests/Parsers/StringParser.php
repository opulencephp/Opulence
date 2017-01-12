<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Console\Requests\Parsers;

use Opulence\Console\Requests\IRequest;
use Opulence\Console\Requests\Tokenizers\StringTokenizer;

/**
 * Defines the string parser
 */
class StringParser extends Parser
{
    /** @var StringTokenizer The tokenizer to use */
    private $tokenizer = null;

    public function __construct()
    {
        $this->tokenizer = new StringTokenizer();
    }

    /**
     * @inheritdoc
     */
    public function parse($input) : IRequest
    {
        $tokens = $this->tokenizer->tokenize($input);

        return $this->parseTokens($tokens);
    }
}
