<?php
/**
 * Copyright (C) 2015 David Young
 *
 * The interface for view readers to implement
 */
namespace Opulence\Views\Factories\IO;

use InvalidArgumentException;

interface IViewReader
{
    /**
     * Reads the contents of a view a the specified path
     *
     * @param string $path The path to the file
     * @return string The contents of the view
     * @throws InvalidArgumentException Thrown if the path is not valid
     */
    public function read($path);
}