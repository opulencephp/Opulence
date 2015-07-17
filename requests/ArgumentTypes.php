<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the different types of arguments
 */
namespace Opulence\Console\Requests;

class ArgumentTypes
{
    /** The argument is required */
    const REQUIRED = 1;
    /** The argument is optional */
    const OPTIONAL = 2;
    /** The argument is an array */
    const IS_ARRAY = 4;
}