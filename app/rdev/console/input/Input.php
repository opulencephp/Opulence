<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines a basic input
 */
namespace RDev\Console\Input;

abstract class Input implements IInput
{
    /** @var array The mapping of argument names to values */
    private $arguments = [];
    /** @var array The mapping of option names to values */
    private $options = [];

    /**
     * {@inheritdoc}
     */
    public function getArgument($name)
    {
        if(!$this->hasArgument($name))
        {
            throw new \InvalidArgumentException("Argument with name \"$name\" does not exist");
        }

        return $this->arguments[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getArguments()
    {
        return $this->arguments;
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
    public function hasArgument($name)
    {
        return isset($this->arguments[$name]);
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
    public function setArgument($name, $value)
    {
        $this->arguments[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }
}