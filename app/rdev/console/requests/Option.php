<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines a console command option
 */
namespace RDev\Console\Requests;

class Option
{
    /** @var string The name of the option */
    private $name = "";
    /** @var int The type of option this is */
    private $type = OptionTypes::REQUIRED_VALUE;
    /** @var string A brief description of the option */
    private $description = "";
    /** @var mixed The default value for the option if it's optional */
    private $defaultValue = null;

    /**
     * @param string $name The name of the option
     * @param int $type The type of option this is
     * @param string $description A brief description of the option
     * @param mixed $defaultValue The default value for the option if it's optional
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
     * Gets whether or not the option value is optional
     *
     * @return bool True if the option value is optional, otherwise false
     */
    public function valueIsOptional()
    {
        return $this->type === OptionTypes::OPTIONAL_VALUE;
    }

    /**
     * Gets whether or not the option value is allowed
     *
     * @return bool True if the option value is allowed, otherwise false
     */
    public function valueIsPermitted()
    {
        return $this->type !== OptionTypes::NO_VALUE;
    }

    /**
     * Gets whether or not the option value is required
     *
     * @return bool True if the option value is required, otherwise false
     */
    public function valueIsRequired()
    {
        return $this->type === OptionTypes::REQUIRED_VALUE;
    }
}