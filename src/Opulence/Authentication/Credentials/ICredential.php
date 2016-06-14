<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Credentials;

use Opulence\Authentication\Tokens\IToken;

/**
 * Defines the interface for a single credential
 */
interface ICredential
{
    /**
     * Deactivates this credential
     */
    public function deactivate();

    /**
     * Gets the Id of the entity whose credential this is
     *
     * @return int|string The Id of the entity whose credential this is
     */
    public function getEntityId();

    /**
     * Gets the Id of the type of entity whose credential this is
     *
     * @return int|string The Id of the type of entity whose credential this is
     */
    public function getEntityTypeId();

    /**
     * Gets the database Id
     *
     * @return int|string The database Id
     */
    public function getId();

    /**
     * Gets the token contained in this credential
     *
     * @return IToken The token contained in this credential
     */
    public function getToken() : IToken;

    /**
     * Gets the type of this credential
     *
     * @return int The type of credential
     */
    public function getTypeId() : int;

    /**
     * Gets whether or not a credential is active
     *
     * @return bool True if the credential is active, otherwise false
     */
    public function isActive() : bool;

    /**
     * @param int|string $entityId
     */
    public function setEntityId($entityId);

    /**
     * Sets the database Id of the credential
     *
     * @param int|string $id The database Id
     */
    public function setId($id);
} 