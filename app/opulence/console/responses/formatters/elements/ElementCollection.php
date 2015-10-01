<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the element collection
 */
namespace Opulence\Console\Responses\Formatters\Elements;

use InvalidArgumentException;

class ElementCollection
{
    /** @var Element[] The list of registered elements */
    private $elements = [];

    /**
     * Adds an element that can be displayed in the response
     *
     * @param Element|Element[] $elements The element or elements to add
     */
    public function add($elements)
    {
        if(!is_array($elements))
        {
            $elements = [$elements];
        }

        /** @var Element $element */
        foreach($elements as $element)
        {
            $this->elements[$element->getName()] = $element;
        }
    }

    /**
     * Gets the registered element with the input name
     *
     * @param string $name The name of the element to get
     * @return Element The registered element with the input name
     * @throws InvalidArgumentException Thrown if no element with the input name exists
     */
    public function getElement($name)
    {
        if(!$this->has($name))
        {
            throw new InvalidArgumentException("No element with name \"$name\" exists");
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
    public function has($name)
    {
        return isset($this->elements[$name]);
    }
}