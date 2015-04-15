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
    /** @var int|string The session Id */
    private $id = "";
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
    public function flush()
    {
        $this->variables = [];
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        if(isset($this->variables[$name]))
        {
            return $this->variables[$name];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
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
    public function offsetExists($name)
    {
        return isset($this->variables[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($name)
    {
        return $this->get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($name, $value)
    {
        if(is_null($name))
        {
            throw new InvalidArgumentException("Name cannot be empty");
        }

        $this->set($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($name)
    {
        unset($this->variables[$name]);
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
    public function set($name, $value)
    {
        $this->variables[$name] = $value;
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
    public function start(array $variables = [])
    {
        $this->variables = $variables;
        $this->hasStarted = true;

        return $this->hasStarted;
    }
} 