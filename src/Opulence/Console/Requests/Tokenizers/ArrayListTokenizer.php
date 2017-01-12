<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Console\Requests\Tokenizers;

use RuntimeException;

/**
 * Defines the array list tokenizer
 */
class ArrayListTokenizer implements ITokenizer
{
    /**
     * @inheritdoc
     */
    public function tokenize($input) : array
    {
        if (!isset($input['name'])) {
            throw new RuntimeException('No command name given');
        }

        if (!isset($input['arguments'])) {
            $input['arguments'] = [];
        }

        if (!isset($input['options'])) {
            $input['options'] = [];
        }

        $tokens = [$input['name']];
        $tokens = array_merge($tokens, $input['arguments']);
        $tokens = array_merge($tokens, $input['options']);

        return $tokens;
    }
}
