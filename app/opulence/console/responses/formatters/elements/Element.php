<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a console element, similar to an HTML element
 */
namespace Opulence\Console\Responses\Formatters\Elements;

class Element
{
    /** @var string The name of the element */
    private $name = "";
    /** @var Style The style of the element */
    private $style = null;

    /**
     * @param string $name The name of the element
     * @param Style $style The style of the element
     */
    public function __construct($name, Style $style)
    {
        $this->name = $name;
        $this->style = $style;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Style
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * @param Style $style
     */
    public function setStyle($style)
    {
        $this->style = $style;
    }
}