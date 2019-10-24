<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Sessions;

use InvalidArgumentException;
use Opulence\Sessions\Ids\Generators\IdGenerator;
use Opulence\Sessions\Ids\Generators\IIdGenerator;

/**
 * Defines a session that persists throughout a transaction on a page
 */
class Session implements ISession
{
    /** The key for new flash keys */
    const NEW_FLASH_KEYS_KEY = '__OPULENCE_NEW_FLASH_KEYS';
    /** The key for stale flash keys */
    const STALE_FLASH_KEYS_KEY = '__OPULENCE_STALE_FLASH_KEYS';

    /** @var int|string The session Id */
    private $id = '';
    /** @var string The session name */
    private $name = '';
    /** @var IIdGenerator The Id generator to use */
    private $idGenerator = null;
    /** @var array The mapping of variable names to values */
    private $vars = [];
    /** @var bool Whether or not the session has started */
    private $hasStarted = false;

    /**
     * @param int|string|null $id The Id of the session
     * @param ?IIdGenerator $idGenerator The Id generator to use
     */
    public function __construct($id = null, IIdGenerator $idGenerator = null)
    {
        if ($idGenerator === null) {
            $idGenerator = new IdGenerator();
        }

        $this->idGenerator = $idGenerator;
        $this->setId($id);
    }

    /**
     * @inheritdoc
     */
    public function ageFlashData()
    {
        foreach ($this->getStaleFlashKeys() as $oldKey) {
            $this->delete($oldKey);
        }

        $this->set(self::STALE_FLASH_KEYS_KEY, $this->getNewFlashKeys());
        $this->set(self::NEW_FLASH_KEYS_KEY, []);
    }

    /**
     * @inheritdoc
     */
    public function delete(string $key)
    {
        unset($this->vars[$key]);
    }

    /**
     * @inheritdoc
     */
    public function flash(string $key, $value)
    {
        $this->set($key, $value);
        $newFlashKeys = $this->getNewFlashKeys();
        $newFlashKeys[] = $key;
        $this->set(self::NEW_FLASH_KEYS_KEY, $newFlashKeys);
        $staleFlashKeys = $this->getStaleFlashKeys();

        // Remove the data from the list of stale keys, if it was there
        if (($staleKey = array_search($key, $staleFlashKeys)) !== false) {
            unset($staleFlashKeys[$staleKey]);
        }

        $this->set(self::STALE_FLASH_KEYS_KEY, $staleFlashKeys);
    }

    /**
     * @inheritdoc
     */
    public function flush()
    {
        $this->vars = [];
    }

    /**
     * @inheritdoc
     */
    public function get(string $key, $defaultValue = null)
    {
        if (isset($this->vars[$key])) {
            return $this->vars[$key];
        }

        return $defaultValue;
    }

    /**
     * @inheritdoc
     */
    public function getAll() : array
    {
        return $this->vars;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function has(string $key) : bool
    {
        return isset($this->vars[$key]);
    }

    /**
     * @return bool
     */
    public function hasStarted() : bool
    {
        return $this->hasStarted;
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($key) : bool
    {
        return $this->has($key);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($key, $value)
    {
        if ($key === null) {
            throw new InvalidArgumentException('Key cannot be empty');
        }

        $this->set($key, $value);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($key)
    {
        unset($this->vars[$key]);
    }

    /**
     * @inheritdoc
     */
    public function reflash()
    {
        $newFlashKeys = $this->getNewFlashKeys();
        $staleFlashKeys = $this->getStaleFlashKeys();
        $this->set(self::NEW_FLASH_KEYS_KEY, array_merge($newFlashKeys, $staleFlashKeys));
        $this->set(self::STALE_FLASH_KEYS_KEY, []);
    }

    /**
     * @inheritdoc
     */
    public function regenerateId()
    {
        $this->setId($this->idGenerator->generate());
    }

    /**
     * @inheritdoc
     */
    public function set(string $key, $value)
    {
        $this->vars[$key] = $value;
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        if ($this->idGenerator->idIsValid($id)) {
            $this->id = $id;
        } else {
            $this->regenerateId();
        }
    }

    /**
     * @inheritdoc
     */
    public function setMany(array $variables)
    {
        $this->vars = array_merge($this->vars, $variables);
    }

    /**
     * @inheritdoc
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @inheritdoc
     */
    public function start(array $vars = []) : bool
    {
        $this->setMany($vars);
        $this->hasStarted = true;

        return $this->hasStarted;
    }

    /**
     * Gets the new flash keys array
     *
     * @return array The list of new flashed keys
     */
    protected function getNewFlashKeys() : array
    {
        return $this->get(self::NEW_FLASH_KEYS_KEY, []);
    }

    /**
     * Gets the stale flash keys array
     *
     * @return array The list of stale flashed keys
     */
    protected function getStaleFlashKeys() : array
    {
        return $this->get(self::STALE_FLASH_KEYS_KEY, []);
    }
}
