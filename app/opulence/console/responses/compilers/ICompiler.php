<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for response compilers to implement
 */
namespace Opulence\Console\Responses\Compilers;

use Opulence\Console\Responses\Compilers\Elements\Style;
use RuntimeException;

interface ICompiler
{
    /**
     * Compiles a message
     *
     * @param string $message The message to compile
     * @return string The compiled message
     * @throws RuntimeException Thrown if there was an issue compiling the message
     */
    public function compile($message);

    /**
     * Registers an element
     *
     * @param string $name The name of the element
     * @param Style $style The style of the element
     */
    public function registerElement($name, Style $style);

    /**
     * Sets whether or not messages should be styled
     *
     * @param bool $isStyled Whether or not messages should be styled
     */
    public function setStyled($isStyled);
}