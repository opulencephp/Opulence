<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication;

/**
 * Defines the interface for principals to implement
 */
interface IPrincipal
{
    /**
     * Gets the identity
     *
     * @return mixed The identity of the principal
     */
    public function getIdentity();

    /**
     * Gets the type of principal this is
     *
     * @return string The type
     */
    public function getType() : string;
}