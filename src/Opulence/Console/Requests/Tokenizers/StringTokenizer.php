<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Console\Requests\Tokenizers;

use RuntimeException;

/**
 * Defines the string tokenizer
 */
class StringTokenizer implements ITokenizer
{
    /**
     * @inheritdoc
     */
    public function tokenize($input) : array
    {
        $input = trim($input);
        $inDoubleQuotes = false;
        $inSingleQuotes = false;
        $charArray = preg_split('//u', $input, -1, PREG_SPLIT_NO_EMPTY);
        $previousChar = '';
        $buffer = '';
        $tokens = [];

        foreach ($charArray as $charIter => $char) {
            switch ($char) {
                case '"':
                    // If the double quote is inside single quotes, we treat it as part of a quoted string
                    if (!$inSingleQuotes) {
                        $inDoubleQuotes = !$inDoubleQuotes;
                    }

                    $buffer .= '"';

                    break;
                case "'":
                    // If the single quote is inside double quotes, we treat it as part of a quoted string
                    if (!$inDoubleQuotes) {
                        $inSingleQuotes = !$inSingleQuotes;
                    }

                    $buffer .= "'";

                    break;
                default:
                    if ($inDoubleQuotes || $inSingleQuotes || $char !== ' ') {
                        $buffer .= $char;
                    } elseif ($char === ' ' && $previousChar !== ' ' && mb_strlen($buffer) > 0) {
                        // We've hit a space outside a quoted string, so flush the buffer
                        $tokens[] = $buffer;
                        $buffer = '';
                    }
            }

            $previousChar = $char;
        }

        // Flush out the buffer
        if (mb_strlen($buffer) > 0) {
            $tokens[] = $buffer;
        }

        if ($inDoubleQuotes || $inSingleQuotes) {
            throw new RuntimeException('Unclosed ' . ($inDoubleQuotes ? 'double' : 'single') . ' quote');
        }

        return $tokens;
    }
}
