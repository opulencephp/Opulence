<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Views\Factories\IO;

use InvalidArgumentException;

/**
 * The interface for view readers to implement
 */
interface IViewReader
{
    /**
     * Reads the contents of a view at the specified path
     *
     * @param string $path The path to the file
     * @return string The contents of the view
     * @throws InvalidArgumentException Thrown if the path is not valid
     */
    public function read(string $path) : string;
}
