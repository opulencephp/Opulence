<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Views\Compilers\Fortune\Lexers\Tokens;

/**
 * Defines the different token types
 */
class TokenTypes
{
    /** Defines an expression token type */
    const T_EXPRESSION = 'T_EXPRESSION';
    /** Defines a PHP open tag token type */
    const T_PHP_TAG_OPEN = 'T_PHP_TAG_OPEN';
    /** Defines a PHP close tag token type */
    const T_PHP_TAG_CLOSE = 'T_PHP_TAG_CLOSE';
    /** Defines a directive open token type */
    const T_DIRECTIVE_OPEN = 'T_DIRECTIVE_OPEN';
    /** Defines a directive name token type */
    const T_DIRECTIVE_NAME = 'T_DIRECTIVE_NAME';
    /** Defines a directive close token type */
    const T_DIRECTIVE_CLOSE = 'T_DIRECTIVE_CLOSE';
    /** Defines a sanitized tag open token type */
    const T_SANITIZED_TAG_OPEN = 'T_SANITIZED_TAG_OPEN';
    /** Defines a sanitized tag close token type */
    const T_SANITIZED_TAG_CLOSE = 'T_SANITIZED_TAG_CLOSE';
    /** Defines an unsanitized tag open token type */
    const T_UNSANITIZED_TAG_OPEN = 'T_UNSANITIZED_TAG_OPEN';
    /** Defines an unsanitized tag close token type */
    const T_UNSANITIZED_TAG_CLOSE = 'T_UNSANITIZED_TAG_CLOSE';
    /** Defines a comment open tag token type */
    const T_COMMENT_OPEN = 'T_COMMENT_OPEN';
    /** Defines a comment close tag token type */
    const T_COMMENT_CLOSE = 'T_COMMENT_CLOSE';
}
