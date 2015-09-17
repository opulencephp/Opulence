<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a host that uses a regex to match against the name
 */
namespace Opulence\Applications\Environments\Hosts;

class HostRegex extends HostName
{
    /**
     * @param string $value The value of the regex, which should not have regex delimiters
     */
    public function __construct($value)
    {
        parent::__construct("#$value#");
    }
}