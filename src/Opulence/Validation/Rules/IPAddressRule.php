<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Validation\Rules;

/**
 * Defines the IP address rule
 */
class IPAddressRule implements IRule
{
    /**
     * @inheritdoc
     */
    public function getSlug() : string
    {
        return "ipAddress";
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = []) : bool
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }
}