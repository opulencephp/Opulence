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
    /** @var string|null The short name of the option if it has one, otherwise null */
    private $shortName = "";
    /** @var int The type of option this is */
    private $type = OptionTypes::REQUIRED_VALUE;
    /** @var string A brief description of the option */
    private $description = "";
    /** @var mixed The default value for the option if it's optional */
    private $defaultValue = null;

    /**
     * @param string $name The name of the option
     * @param string|null $shortName The short name of the option if it has one, otherwise null
     * @param int $type The type of option this is
     * @param string $description A brief description of the option
     * @param mixed $defaultValue The default value for the option if it's optional
     * @throws \InvalidArgumentException Thrown if the type is invalid
     */
    public function __construct($name, $shortName, $type, $description, $defaultValue = null)
    {
        if(($type & 3) === 3)
        {
            throw new \InvalidArgumentException("Option type cannot be both optional and required");
        }
        elseif(($type & 5) === 5 || ($type & 6) === 6)
        {
            throw new \InvalidArgumentException("Option cannot have a value and not have a value");
        }

        $this->name = $name;
        $this->shortName = $shortName;
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
     * @return null|string
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * Gets whether or not the option value is an array
     *
     * @return bool True if the option value is an array, otherwise false
     */
    public function valueIsArray()
    {
        return ($this->type & OptionTypes::IS_ARRAY) === OptionTypes::IS_ARRAY;
    }

    /**
     * Gets whether or not the option value is optional
     *
     * @return bool True if the option value is optional, otherwise false
     */
    public function valueIsOptional()
    {
        return ($this->type & OptionTypes::OPTIONAL_VALUE) === OptionTypes::OPTIONAL_VALUE;
    }

    /**
     * Gets whether or not the option value is allowed
     *
     * @return bool True if the option value is allowed, otherwise false
     */
    public function valueIsPermitted()
    {
        return ($this->type & OptionTypes::NO_VALUE) !== OptionTypes::NO_VALUE;
    }

    /**
     * Gets whether or not the option value is required
     *
     * @return bool True if the option value is required, otherwise false
     */
    public function valueIsRequired()
    {
        return ($this->type & OptionTypes::REQUIRED_VALUE) === OptionTypes::REQUIRED_VALUE;
    }
}