<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Environments\Hosts;

/**
 * Defines a host that uses a regex to match against the name
 */
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