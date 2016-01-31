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
 * Defines a single credential
 */
class Credential implements ICredential
{
    /** @var int|string The database Id of this credential */
    private $id = -1;
    /** @var int|string The Id of the entity whose credential this is */
    private $entityId = -1;
    /** @var int The Id of the type of entity whose credential this is */
    private $entityTypeId = -1;
    /** @var IToken The token contained in this credential */
    private $token = null;
    /** @var int The type of credential this is */
    private $typeId = -1;

    /**
     * @param int|string $id The database Id of this credential
     * @param int $typeId The type of credential this is
     * @param int $entityId The Id of the entity whose credential this is
     * @param int $entityTypeId The Id of the type of entity whose credential this is
     * @param IToken $token The contained in this credential
     */
    public function __construct($id, int $typeId, int $entityId, int $entityTypeId, IToken $token)
    {
        $this->setId($id);
        $this->typeId = $typeId;
        $this->setEntityId($entityId);
        $this->entityTypeId = $entityTypeId;
        $this->token = $token;
    }

    /**
     * Clones the token contained in this class
     */
    public function __clone()
    {
        $this->token = clone $this->token;
    }

    /**
     * @inheritdoc
     */
    public function deactivate()
    {
        $this->token->deactivate();
    }

    /**
     * @inheritdoc
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * @inheritdoc
     */
    public function getEntityTypeId() : int
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
     * @inheritdoc
     */
    public function getToken() : IToken
    {
        return $this->token;
    }

    /**
     * @inheritdoc
     */
    public function getTypeId() : int
    {
        return $this->typeId;
    }

    /**
     * @inheritdoc
     */
    public function isActive() : bool
    {
        return $this->token->isActive();
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->id = $id;
    }
} 