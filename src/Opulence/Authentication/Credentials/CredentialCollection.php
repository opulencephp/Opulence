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
 * Defines methods and properties for all the credentials an entity has
 */
class CredentialCollection implements ICredentialCollection
{
    /** @var int|string The Id of the entity whose credentials these are */
    private $entityId = -1;
    /** @var int The Id of the type of entity whose credentials these are */
    private $entityTypeId = -1;
    /** @var ICredential[] The credential type to credential mapping */
    private $credentials = [];
    /** @var ICredentialStorage[] The credential type to storage mechanism mapping */
    private $storages = [];

    /**
     * @param int|string $entityId The Id of the entity whose credentials these are
     * @param int $entityTypeId The Id of the type of entity whose credentials these are
     * @param array $storages The type Id to storage mechanism for the various types of credentials
     * @param ICredential[] $credentials The list of credentials to add
     * @throws RuntimeException Thrown if a credential was added that didn't have a storage mechanism registered
     */
    public function __construct($entityId = -1, int $entityTypeId = -1, array $storages = [], array $credentials = [])
    {
        $this->setEntityId($entityId);
        $this->setEntityTypeId($entityTypeId);

        foreach ($storages as $type => $storage) {
            $this->registerStorage($type, $storage);
        }

        foreach ($credentials as $credential) {
            $this->add($credential);
        }
    }

    /**
     * @inheritdoc
     */
    public function add(ICredential $credential)
    {
        if (!isset($this->storages[$credential->getTypeId()])) {
            throw new RuntimeException("No storage for credential type {$credential->getTypeId()}");
        }

        if ($credential->isActive()) {
            $this->credentials[$credential->getTypeId()] = $credential;
        }
    }

    /**
     * @inheritdoc
     */
    public function delete(Response $response, int $type)
    {
        if (!isset($this->storages[$type])) {
            throw new RuntimeException("No storage for credential type $type");
        }

        $this->credentials[$type]->deactivate();
        $this->storages[$type]->delete($response);
        unset($this->credentials[$type]);
    }

    /**
     * @inheritdoc
     */
    public function get(int $type)
    {
        if (!$this->has($type)) {
            return null;
        }

        $credential = $this->credentials[$type];

        // Don't return deactivated credentials
        if (!$credential->isActive()) {
            return null;
        }

        return $credential;
    }

    /**
     * @inheritdoc
     */
    public function getAll() : array
    {
        return array_values($this->credentials);
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
     * @inheritdoc
     */
    public function getTypes() : array
    {
        return array_keys($this->credentials);
    }

    /**
     * @inheritdoc
     */
    public function has(int $type) : bool
    {
        if (isset($this->credentials[$type])) {
            return true;
        }

        if (!isset($this->storages[$type])) {
            return false;
        }

        if (!$this->storages[$type]->exists()) {
            return false;
        }

        $this->add($this->storages[$type]->get());

        return true;
    }

    /**
     * @inheritdoc
     */
    public function registerStorage(int $type, Storage\ICredentialStorage $storage)
    {
        $this->storages[$type] = $storage;
    }

    /**
     * @inheritdoc
     */
    public function save(Response $response, ICredential $credential, string $unhashedToken)
    {
        if (!isset($this->storages[$credential->getTypeId()])) {
            throw new RuntimeException("No storage for credential type {$credential->getTypeId()}");
        }

        $this->storages[$credential->getTypeId()]->save($response, $credential, $unhashedToken);
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
    public function setEntityTypeId(int $entityTypeId)
    {
        $this->entityTypeId = $entityTypeId;
    }
} 