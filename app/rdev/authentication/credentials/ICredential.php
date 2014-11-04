<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for a single credential
 */
namespace RDev\Authentication\Credentials;
use RDev\Cryptography;
use RDev\ORM;

interface ICredential extends ORM\IEntity
{
    /**
     * Deactivates this credential
     */
    public function deactivate();

    /**
     * Gets the Id of the entity whose credential this is
     *
     * @return string|int The Id of the entity whose credential this is
     */
    public function getEntityId();

    /**
     * Gets the Id of the type of entity whose credential this is
     *
     * @return string|int The Id of the type of entity whose credential this is
     */
    public function getEntityTypeId();

    /**
     * Gets the token contained in this credential
     *
     * @return Cryptography\IToken The token contained in this credential
     */
    public function getToken();

    /**
     * Gets the type of this credential
     *
     * @return int The type of credential
     */
    public function getTypeId();

    /**
     * Gets whether or not a credential is active
     *
     * @return bool True if the credential is active, otherwise false
     */
    public function isActive();

    /**
     * @param int|string $entityId
     */
    public function setEntityId($entityId);
} 