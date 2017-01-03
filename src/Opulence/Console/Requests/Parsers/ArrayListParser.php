<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Console\Requests\Parsers;

use InvalidArgumentException;
use Opulence\Console\Requests\IRequest;
use Opulence\Console\Requests\Tokenizers\ArrayListTokenizer;

/**
 * Defines the array list parser
 */
class ArrayListParser extends Parser
{
    /** @var ArrayListTokenizer The tokenizer to use */
    private $tokenizer = null;

    public function __construct()
    {
        $this->tokenizer = new ArrayListTokenizer();
    }

    /**
     * @inheritdoc
     */
    public function parse($input) : IRequest
    {
        if (!is_array($input)) {
            throw new InvalidArgumentException(__METHOD__ . " only accepts arrays as input");
        }

        $tokens = $this->tokenizer->tokenize($input);

        return $this->parseTokens($tokens);
    }
}