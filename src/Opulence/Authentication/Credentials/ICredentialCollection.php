<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Credentials;

use Opulence\Authentication\Credentials\Storage\ICredentialStorage;
use Opulence\Http\Responses\Response;
use RuntimeException;

/**
 * Defines the interface for credential collections to implement
 */
interface ICredentialCollection
{
    /**
     * Adds a credential
     * If the credential has expired, then it is not added
     *
     * @param ICredential $credential The credential to add
     * @throws RuntimeException Thrown if the credential didn't have a storage mechanism registered
     */
    public function add(ICredential $credential);

    /**
     * Removes a credential of the input type
     *
     * @param Response $response The response to delete the credential from
     * @param int $type The type of credential to remove
     * @throws RuntimeException Thrown if the credential didn't have a storage mechanism registered
     */
    public function delete(Response $response, int $type);

    /**
     * Gets the credential of the input type
     * Only active credentials are returned
     *
     * @param int $type The type of credential we want
     * @return ICredential|null An instance of the input type if it exists, otherwise null
     * @throws InvalidCredentialException Thrown if the credentials exist but are invalid
     */
    public function get(int $type);

    /**
     * Gets all of the credentials contained
     *
     * @return ICredential[] The list of credentials
     */
    public function getAll() : array;

    /**
     * Gets the Id of the entity whose credentials these are
     *
     * @return int|string The Id of the entity whose credentials these are
     */
    public function getEntityId();

    /**
     * Gets the type Id of the entity whose credentials these are
     *
     * @return int The Id of the type of entity whose credentials these are
     */
    public function getEntityTypeId() : int;

    /**
     * Gets all of the types of credentials contained
     *
     * @return array The list of all the types of credentials
     */
    public function getTypes() : array;

    /**
     * Gets whether or not there exists a credential with the input type
     *
     * @param int $type The type of credential to search for
     * @return bool True if it exists, otherwise false
     * @throws InvalidCredentialException Thrown if the credentials exist but are invalid
     */
    public function has(int $type) : bool;

    /**
     * Registers a storage mechanism for a type of credential
     *
     * @param int $type The type of credential whose storage mechanism we're registering
     * @param ICredentialStorage $storage The storage mechanism
     */
    public function registerStorage(int $type, ICredentialStorage $storage);

    /**
     * Saves a credential to storage
     *
     * @param Response $response The response to save the credential to
     * @param ICredential $credential The credential to save
     * @param string $unhashedToken The unhashed token value
     * @throws RuntimeException Thrown if the credential didn't have a storage mechanism registered
     */
    public function save(Response $response, ICredential $credential, string $unhashedToken);

    /**
     * Sets the entity Id
     *
     * @param int|string $entityId The Id of the entity whose credentials these are
     */
    public function setEntityId($entityId);

    /**
     * Sets the entity type Id
     *
     * @param int $entityTypeId The Id of the type of entity whose credentials these are
     */
    public function setEntityTypeId(int $entityTypeId);
} 