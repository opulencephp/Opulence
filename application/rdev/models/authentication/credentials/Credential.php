<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a single credential
 */
namespace RDev\Models\Authentication\Credentials;
use RDev\Models\Cryptography;

class Credential implements ICredential
{
    /** @var int|string The database Id of this credential */
    private $id = -1;
    /** @var int|string The Id of the entity whose credential this is */
    private $entityId = -1;
    /** @var int The Id of the type of entity whose credential this is */
    private $entityTypeId = -1;
    /** @var Cryptography\IToken The token contained in this credential */
    private $token = null;
    /** @var int The type of credential this is */
    private $typeId = -1;

    /**
     * @param int|string $id The database Id of this credential
     * @param int $typeId The type of credential this is
     * @param int $entityId The Id of the entity whose credential this is
     * @param int $entityTypeId The Id of the type of entity whose credential this is
     * @param Cryptography\IToken $token The contained in this credential
     */
    public function __construct($id, $typeId, $entityId, $entityTypeId, Cryptography\IToken $token)
    {
        $this->setId($id);
        $this->typeId = $typeId;
        $this->setEntityId($entityId);
        $this->entityTypeId = $entityTypeId;
        $this->token = $token;
    }

    /**
     * {@inheritdoc}
     */
    public function deactivate()
    {
        $this->token->deactivate();
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityTypeId()
    {
        return $this->entityTypeId;
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return $this->token->isActive();
    }

    /**
     * {@inheritdoc}
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
    }
} 