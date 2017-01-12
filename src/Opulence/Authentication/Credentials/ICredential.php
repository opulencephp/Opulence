<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Authentication\Credentials;

/**
 * Defines the interface for credentials to implement
 */
interface ICredential
{
    /**
     * Gets the type of credential this is
     *
     * @return string The type of credential this is
     */
    public function getType() : string;

    /**
     * Gets the value for the input name
     *
     * @param string $name The name of the value to get
     * @return mixed|null The value, if any exists, otherwise null
     */
    public function getValue(string $name);

    /**
     * Gets the list of credential values
     *
     * @return array The mapping of value names to their values
     */
    public function getValues() : array;
}
