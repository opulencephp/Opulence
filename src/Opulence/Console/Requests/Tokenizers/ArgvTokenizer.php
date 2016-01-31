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
 * Defines the argv tokenizer
 */
class ArgvTokenizer implements ITokenizer
{
    /**
     * @inheritdoc
     */
    public function tokenize($input) : array
    {
        // Get rid of the application name
        array_shift($input);

        foreach ($input as &$token) {
            $token = stripslashes($token);
        }

        return $input;
    }
}