<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Environments\Hosts;

/**
 * Defines a host that uses a regex to match against the name
 *
 * @deprecated since 1.0.0-beta7
 */
class HostRegex extends HostName
{
    /**
     * @param string $value The value of the regex, which should not have regex delimiters
     */
    public function __construct(string $value)
    {
        parent::__construct("#$value#");
    }
}