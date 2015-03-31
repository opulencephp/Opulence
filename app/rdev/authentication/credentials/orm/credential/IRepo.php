<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for credential repos to implement
 */
namespace RDev\Authentication\Credentials\ORM\Credential;
use RDev\Authentication\Credentials\ICredential;
use RDev\ORM\Repositories\IRepo as IORMRepo;

interface IRepo extends IORMRepo
{
    /**
     * Gets all the active credentials of the input type that belong to an entity
     *
     * @param int|string $entityId The Id of the entity
     * @param int $entityTypeId The type of entity
     * @param int $credentialTypeId The type of the credentials to return
     * @return ICredential[] The list of all credentials owned by the entity
     */
    public function getAllActiveByEntityId($entityId, $entityTypeId, $credentialTypeId);
} 