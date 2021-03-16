<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Views\Factories\IO;

use InvalidArgumentException;

/**
 * Defines the file view reader
 */
class FileViewReader implements IViewReader
{
    /**
     * @inheritdoc
     */
    public function read(string $path) : string
    {
        if (!file_exists($path)) {
            throw new InvalidArgumentException("Path $path does not exist");
        }

        return file_get_contents($path);
    }
}
