<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Console\Requests;

use InvalidArgumentException;

/**
 * Defines a basic console request
 */
class Request implements IRequest
{
    /** @var string The name of the command entered */
    private $commandName = "";
    /** @var array The list of argument values */
    private $arguments = [];
    /** @var array The mapping of option names to values */
    private $options = [];

    /**
     * @inheritdoc
     */
    public function addArgumentValue($value)
    {
        $this->arguments[] = $value;
    }

    /**
     * @inheritdoc
     */
    public function addOptionValue($name, $value)
    {
        if ($this->optionIsSet($name)) {
            // We now consider this option to have multiple values
            if (!is_array($this->options[$name])) {
                $this->options[$name] = [$this->options[$name]];
            }

            $this->options[$name][] = $value;
        } else {
            $this->options[$name] = $value;
        }
    }

    /**
     * @inheritdoc
     */
    public function getArgumentValues()
    {
        return $this->arguments;
    }

    /**
     * @inheritdoc
     */
    public function getCommandName()
    {
        return $this->commandName;
    }

    /**
     * @inheritdoc
     */
    public function getOptionValue($name)
    {
        if (!$this->optionIsSet($name)) {
            throw new InvalidArgumentException("Option with name \"$name\" does not exist");
        }

        return $this->options[$name];
    }

    /**
     * @inheritdoc
     */
    public function getOptionValues()
    {
        return $this->options;
    }

    /**
     * @inheritdoc
     */
    public function optionIsSet($name)
    {
        // Don't use isset because the value very well might be null, in which case we'd still return true
        return array_key_exists($name, $this->options);
    }

    /**
     * @inheritdoc
     */
    public function setCommandName($name)
    {
        $this->commandName = $name;
    }
}