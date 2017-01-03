<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Console\Requests;

use InvalidArgumentException;

/**
 * Defines a console command argument
 */
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
     * @throws InvalidArgumentException Thrown if the type is invalid
     */
    public function __construct(string $name, int $type, string $description, $defaultValue = null)
    {
        if (($type & 3) === 3) {
            throw new InvalidArgumentException("Argument type cannot be both optional and required");
        }

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
    public function getDescription() : string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Gets whether or not the argument is an array
     *
     * @return bool True if the argument is an array, otherwise false
     */
    public function isArray() : bool
    {
        return ($this->type & ArgumentTypes::IS_ARRAY) === ArgumentTypes::IS_ARRAY;
    }

    /**
     * Gets whether or not the argument is optional
     *
     * @return bool True if the argument is optional, otherwise false
     */
    public function isOptional() : bool
    {
        return ($this->type & ArgumentTypes::OPTIONAL) === ArgumentTypes::OPTIONAL;
    }

    /**
     * Gets whether or not the argument is required
     *
     * @return bool True if the argument is required, otherwise false
     */
    public function isRequired() : bool
    {
        return ($this->type & ArgumentTypes::REQUIRED) === ArgumentTypes::REQUIRED;
    }
}