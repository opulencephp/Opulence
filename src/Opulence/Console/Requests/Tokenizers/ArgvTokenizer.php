<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Console\Requests\Tokenizers;

/**
 * Defines the argv tokenizer
 */
class ArgvTokenizer implements ITokenizer
{
    /**
     * @inheritdoc
     */
    public function tokenize($input): array
    {
        // Get rid of the application name
        array_shift($input);

        foreach ($input as &$token) {
            // We don't use stripslashes() because we want to backslashes when they're not escaping quotes
            $token = str_replace(["\\'", '\\"'], ["'", '"'], $token);
        }

        return $input;
    }
}
