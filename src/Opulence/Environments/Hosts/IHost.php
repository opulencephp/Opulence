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
 * Defines the interface for hosts to implement
 *
 * @deprecated since 1.0.0-beta7
 */
interface IHost
{
    /**
     * Gets the value of the host name
     *
     * @return string The host
     */
    public function getValue() : string;
}