<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the file view reader
 */
namespace Opulence\Views\Factories\IO;

use InvalidArgumentException;

class FileViewReader implements IViewReader
{
    /**
     * @inheritdoc
     */
    public function read($path)
    {
        if (!file_exists($path)) {
            throw new InvalidArgumentException("Path $path does not exist");
        }

        return file_get_contents($path);
    }
}