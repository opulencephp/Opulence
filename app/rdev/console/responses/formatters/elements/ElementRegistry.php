<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the element registry
 */
namespace RDev\Console\Responses\Formatters\Elements;

class ElementRegistry
{
    /** @var Element[] The list of registered elements */
    private $elements = [];

    /**
     * Gets the registered element with the input name
     *
     * @param string $name The name of the element to get
     * @return Element The registered element with the input name
     * @throws \InvalidArgumentException Thrown if no element with the input name exists
     */
    public function getElement($name)
    {
        if(!$this->isRegistered($name))
        {
            throw new \InvalidArgumentException("No element with name \"$name\" exists");
        }

        return $this->elements[$name];
    }

    /**
     * Gets the list of registered elements
     *
     * @return Element[] The list of registered elements
     */
    public function getElements()
    {
        return array_values($this->elements);
    }

    /**
     * Gets whether or not an element with the input name is registered
     *
     * @param string $name The name to search for
     * @return bool True if an element is registered with the name, otherwise false
     */
    public function isRegistered($name)
    {
        return isset($this->elements[$name]);
    }

    /**
     * Registers an element that can be displayed in the response
     *
     * @param Element $element The element to register
     */
    public function registerElement(Element $element)
    {
        $this->elements[$element->getName()] = $element;
    }
}