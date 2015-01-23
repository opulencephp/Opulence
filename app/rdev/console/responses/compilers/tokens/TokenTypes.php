<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the different token types
 */
namespace RDev\Console\Responses\Compilers\Tokens;

class TokenTypes
{
    /** Defines an unknown token type */
    const T_UNKNOWN = 1;
    /** The end of file token type */
    const T_EOF = 2;
    /** Defines a word token type */
    const T_WORD = 3;
    /** Defines an open tag token type */
    const T_TAG_OPEN = 4;
    /** Defines a close tag token type */
    const T_TAG_CLOSE = 5;
}