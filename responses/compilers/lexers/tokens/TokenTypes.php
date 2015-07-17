<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the different token types
 */
namespace Opulence\Console\Responses\Compilers\Lexers\Tokens;

class TokenTypes
{
    /** Defines an unknown token type */
    const T_UNKNOWN = "T_UNKNOWN";
    /** Defines an end of file token type */
    const T_EOF = "T_EOF";
    /** Defines a word token type */
    const T_WORD = "T_WORD";
    /** Defines an open tag token type */
    const T_TAG_OPEN = "T_TAG_OPEN";
    /** Defines a close tag token type */
    const T_TAG_CLOSE = "T_TAG_CLOSE";
}