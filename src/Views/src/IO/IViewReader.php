<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Views\IO;

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
    public function read(string $path): string;
}
