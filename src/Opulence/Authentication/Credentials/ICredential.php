<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Credentials;

/**
 * Defines the interface for credentials to implement
 */
interface ICredential
{
    /**
     * Gets the type Id
     *
     * @return int The type Id
     */
    public function getTypeId() : int;

    /**
     * Gets the list of credential values
     *
     * @return array The list of values that make up the credentials
     */
    public function getValues() : array;
}