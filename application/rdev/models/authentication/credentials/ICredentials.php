<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for credentials to implement
 */
namespace RDev\Models\Authentication\Credentials;

interface ICredentials
{
    /**
     * Adds a credential
     * If the credential has expired, then it is not added
     *
     * @param ICredential $credential The credential to add
     * @throws \RuntimeException Thrown if the credential didn't have a storage mechanism registered
     */
    public function add(ICredential $credential);

    /**
     * Removes a credential of the input type
     *
     * @param int $type The type of credential to remove
     * @throws \RuntimeException Thrown if the credential didn't have a storage mechanism registered
     */
    public function delete($type);

    /**
     * Gets the credential of the input type
     * Only active credentials are returned
     *
     * @param int $type The type of credential we want
     * @return ICredential|null An instance of the input type if it exists, otherwise null
     */
    public function get($type);

    /**
     * Gets all of the credentials contained
     *
     * @return ICredential[] The list of credentials
     */
    public function getAll();

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
    public function getEntityTypeId();

    /**
     * Gets all of the types of credentials contained
     *
     * @return array The list of all the types of credentials
     */
    public function getTypes();

    /**
     * Gets whether or not there exists a credential with the input type
     *
     * @param int $type The type of credential to search for
     * @return bool True if it exists, otherwise false
     */
    public function has($type);

    /**
     * Registers a storage mechanism for a type of credential
     *
     * @param int $type The type of credential whose storage mechanism we're registering
     * @param Storage\ICredentialStorage $storage The storage mechanism
     */
    public function registerStorage($type, Storage\ICredentialStorage $storage);

    /**
     * Saves a credential to storage
     *
     * @param ICredential $credential The credential to save
     * @param string $unhashedToken The unhashed token value
     * @throws \RuntimeException Thrown if the credential didn't have a storage mechanism registered
     */
    public function save(ICredential $credential, $unhashedToken);
} 