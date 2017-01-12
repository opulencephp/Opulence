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
 * Defines the interface for view name resolvers to implement
 */
interface IViewNameResolver
{
    /**
     * Registers an extension to match against when resolving a view name
     *
     * @param string $extension The extension to register
     * @param int $priority The priority of this extension when matching it (lower numbers mean higher priority)
     */
    public function registerExtension(string $extension, int $priority = -1);

    /**
     * Registers a path to match against when resolving a view name
     *
     * @param string $path The path to register
     * @param int $priority The priority of this path when matching it (lower numbers mean higher priority)
     */
    public function registerPath(string $path, int $priority = -1);

    /**
     * Resolves a view name by matching it against registered extensions and paths
     *
     * @param string $name The view name to resolve
     * @return string The resolved view name
     * @throws InvalidArgumentException Thrown if the name could not be resolved
     */
    public function resolve(string $name) : string;
}
