<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Views\Factories\IO;

use InvalidArgumentException;

/**
 * Defines the file view reader
 */
final class FileViewReader implements IViewReader
{
    /**
     * @inheritdoc
     */
    public function read(string $path): string
    {
        if (!file_exists($path)) {
            throw new InvalidArgumentException("Path $path does not exist");
        }

        return file_get_contents($path);
    }
}
