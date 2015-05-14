<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a session that persists throughout a transaction on a page
 */
namespace RDev\Sessions;
use InvalidArgumentException;
use RDev\Cryptography\Utilities\Strings;
use RDev\Sessions\Ids\IIdGenerator;
use RDev\Sessions\Ids\IdGenerator;

class Session implements ISession
{
    /** The key for new flash keys */
    const NEW_FLASH_KEYS_KEY = "__RDEV_NEW_FLASH_KEYS";
    /** The key for stale flash keys */
    const STALE_FLASH_KEYS_KEY = "__RDEV_STALE_FLASH_KEYS";
    /** @var int|string The session Id */
    private $id = "";
    /** @var string The session name */
    private $name = "";
    /** @var IIdGenerator The Id generator to use */
    private $idGenerator = null;
    /** @var array The mapping of variable names to values */
    private $variables = [];
    /** @var bool Whether or not the session has started */
    private $hasStarted = false;

    /**
     * @param int|string|null $id The Id of the session
     * @param IIdGenerator $idGenerator The Id generator to use
     */
    public function __construct($id = null, IIdGenerator $idGenerator = null)
    {
        if(!is_null($id))
        {
            $this->setId($id);
        }

        if(is_null($idGenerator))
        {
            $idGenerator = new IdGenerator(new Strings());
        }

        $this->idGenerator = $idGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function ageFlashData()
    {
        foreach($this->getStaleFlashKeys() as $oldKey)
        {
            $this->delete($oldKey);
        }

        $this->set(self::STALE_FLASH_KEYS_KEY, $this->getNewFlashKeys());
        $this->set(self::NEW_FLASH_KEYS_KEY, []);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        unset($this->variables[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function flash($key, $value)
    {
        $this->set($key, $value);
        $newFlashKeys = $this->getNewFlashKeys();
        $newFlashKeys[] = $key;
        $this->set(self::NEW_FLASH_KEYS_KEY, $newFlashKeys);
        $staleFlashKeys = $this->getStaleFlashKeys();

        // Remove the data from the list of stale keys, if it was there
        if(($staleKey = array_search($key, $staleFlashKeys)) !== false)
        {
            unset($staleFlashKeys[$staleKey]);
        }

        $this->set(self::STALE_FLASH_KEYS_KEY, $staleFlashKeys);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->variables = [];
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $defaultValue = null)
    {
        if(isset($this->variables[$key]))
        {
            return $this->variables[$key];
        }

        return $defaultValue;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        return $this->variables;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return isset($this->variables[$key]);
    }

    /**
     * @return boolean
     */
    public function hasStarted()
    {
        return $this->hasStarted;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($key, $value)
    {
        if(is_null($key))
        {
            throw new InvalidArgumentException("Key cannot be empty");
        }

        $this->set($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($key)
    {
        unset($this->variables[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function reflash()
    {
        $newFlashKeys = $this->getNewFlashKeys();
        $staleFlashKeys = $this->getStaleFlashKeys();
        $this->set(self::NEW_FLASH_KEYS_KEY, array_merge($newFlashKeys, $staleFlashKeys));
        $this->set(self::STALE_FLASH_KEYS_KEY, []);
    }

    /**
     * {@inheritdoc}
     */
    public function regenerateId()
    {
        $this->setId($this->idGenerator->generate());
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $this->variables[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function setMany(array $variables)
    {
        $this->variables = array_merge($this->variables, $variables);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function start(array $variables = [])
    {
        $this->variables = $variables;
        $this->hasStarted = true;

        return $this->hasStarted;
    }

    /**
     * Gets the new flash keys array
     *
     * @return array The list of new flashed keys
     */
    protected function getNewFlashKeys()
    {
        return $this->get(self::NEW_FLASH_KEYS_KEY, []);
    }

    /**
     * Gets the stale flash keys array
     *
     * @return array The list of stale flashed keys
     */
    protected function getStaleFlashKeys()
    {
        return $this->get(self::STALE_FLASH_KEYS_KEY, []);
    }
} 