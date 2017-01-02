<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Clients;

/**
 * Defines the interface for clients to implement
 */
interface IClient
{
    /**
     * Gets the client Id
     *
     * @return int|string The client Id
     */
    public function getId();

    /**
     * Gets the client name
     *
     * @return string The client name
     */
    public function getName() : string;

    /**
     * Gets the client secret
     *
     * @return string The client secret
     */
    public function getSecret() : string;

    /**
     * Sets the client Id
     *
     * @param int|string $id The client Id
     */
    public function setId($id);

    /**
     * Sets the client name
     *
     * @param string $name The client name
     */
    public function setName(string $name);
}