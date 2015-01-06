<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the different types of options
 */
namespace RDev\Console\Requests;

class OptionTypes
{
    /** The argument is required */
    const REQUIRED_VALUE = 1;
    /** The argument is optional */
    const OPTIONAL_VALUE = 2;
    /** The argument is not allowed */
    const NO_VALUE = 3;
}