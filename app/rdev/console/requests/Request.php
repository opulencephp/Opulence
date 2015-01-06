<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines a basic request
 */
namespace RDev\Console\Requests;

class Request implements IRequest
{
    /** @var string The name of the command entered */
    private $commandName = "";
    /** @var array The list of argument values */
    private $arguments = [];
    /** @var array The mapping of option names to values */
    private $options = [];

    /**
     * {@inheritdoc}
     */
    public function addArgumentValue($value)
    {
        $this->arguments[] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getArgumentValues()
    {
        return $this->arguments;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandName()
    {
        return $this->commandName;
    }

    /**
     * {@inheritdoc}
     */
    public function getOption($name)
    {
        if(!$this->hasOption($name))
        {
            throw new \InvalidArgumentException("Option with name \"$name\" does not exist");
        }

        return $this->options[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function hasOption($name)
    {
        return isset($this->options[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function setCommandName($name)
    {
        $this->commandName = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }
}