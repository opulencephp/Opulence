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
        if(!isset($this->elements[$name]))
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
     * Registers an element that can be displayed in the response
     *
     * @param Element $element The element to register
     */
    public function registerElement(Element $element)
    {
        $this->elements[$element->getName()] = $element;
    }
}