<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Console\Requests\Parsers;

use InvalidArgumentException;
use Opulence\Console\Requests\IRequest;
use Opulence\Console\Requests\Tokenizers\ArgvTokenizer;

/**
 * Defines the argv parser
 */
class ArgvParser extends Parser
{
    /** @var ArgvTokenizer The tokenizer to use */
    private $tokenizer;

    public function __construct()
    {
        $this->tokenizer = new ArgvTokenizer();
    }

    /**
     * @inheritdoc
     */
    public function parse($input): IRequest
    {
        if ($input === null) {
            $input = $_SERVER['argv'];
        }

        if (!is_array($input)) {
            throw new InvalidArgumentException('ArgvParser parser only accepts arrays as input');
        }

        $tokens = $this->tokenizer->tokenize($input);

        return $this->parseTokens($tokens);
    }
}
