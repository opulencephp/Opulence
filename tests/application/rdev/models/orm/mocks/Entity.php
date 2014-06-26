<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the entity class for use in testing
 */
namespace RDev\Tests\Models\ORM\Mocks;
use RDev\Models;

class Entity implements Models\IEntity
{
    /** @var int The Id of the entity */
    private $id = -1;
    /** @var string The test string property */
    private $stringProperty = "";

    /**
     * @param int $id The Id of the entity
     * @param string $stringProperty The test string property
     */
    public function __construct($id, $stringProperty)
    {
        $this->id = $id;
        $this->stringProperty = $stringProperty;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getStringProperty()
    {
        return $this->stringProperty;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param string $stringProperty
     */
    public function setStringProperty($stringProperty)
    {
        $this->stringProperty = $stringProperty;
    }
} 