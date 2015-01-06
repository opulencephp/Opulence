<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines a console command argument
 */
namespace RDev\Console\Requests;

class Argument
{
    /** @var string The name of the argument */
    private $name = "";
    /** @var int The type of argument this is */
    private $type = ArgumentTypes::REQUIRED;
    /** @var string A brief description of the argument */
    private $description = "";
    /** @var mixed The default value for the argument if it's optional */
    private $defaultValue = null;

    /**
     * @param string $name The name of the argument
     * @param int $type The type of argument this is
     * @param string $description A brief description of the argument
     * @param mixed $defaultValue The default value for the argument if it's optional
     */
    public function __construct($name, $type, $description, $defaultValue = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->description = $description;
        $this->defaultValue = $defaultValue;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Gets whether or not the argument is optional
     *
     * @return bool True if the argument is optional, otherwise false
     */
    public function isOptional()
    {
        return $this->type === ArgumentTypes::OPTIONAL;
    }

    /**
     * Gets whether or not the argument is required
     *
     * @return bool True if the argument is required, otherwise false
     */
    public function isRequired()
    {
        return $this->type === ArgumentTypes::REQUIRED;
    }
}