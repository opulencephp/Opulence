<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for credential repos to implement
 */
namespace RDev\Models\Authentication\Credentials\ORM\Credential;
use RDev\Models\Authentication\Credentials;
use RDev\Models\ORM\Repositories;

interface IRepo extends Repositories\IRepo
{
    /**
     * Gets all the active credentials that belong to an entity
     *
     * @param int|string $entityId The Id of the entity
     * @param int $entityTypeId The type of entity
     * @return Credentials\Credential[] The list of all credentials owned by the entity
     */
    public function getAllActiveByEntityId($entityId, $entityTypeId);
} 