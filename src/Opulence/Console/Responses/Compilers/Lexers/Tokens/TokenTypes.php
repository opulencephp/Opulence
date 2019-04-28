<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Console\Responses\Compilers\Lexers\Tokens;

/**
 * Defines the different token types
 */
class TokenTypes
{
    /** Defines an unknown token type */
    public const T_UNKNOWN = 'T_UNKNOWN';
    /** Defines an end of file token type */
    public const T_EOF = 'T_EOF';
    /** Defines a word token type */
    public const T_WORD = 'T_WORD';
    /** Defines an open tag token type */
    public const T_TAG_OPEN = 'T_TAG_OPEN';
    /** Defines a close tag token type */
    public const T_TAG_CLOSE = 'T_TAG_CLOSE';
}
