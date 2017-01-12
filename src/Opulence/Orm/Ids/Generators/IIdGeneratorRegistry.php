<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Orm\Ids\Generators;

/**
 * Defines the interface for Id generator registries to implement
 */
interface IIdGeneratorRegistry
{
    /**
     * Gets the Id generator for the input class
     *
     * @param string $className The class whose Id generator we want
     * @return IIdGenerator|null The Id generator if one exists for the class, otherwise null
     */
    public function getIdGenerator(string $className);

    /**
     * Registers the Id generator for all instances of the input class
     *
     * @param string $className The name of the class
     * @param IIdGenerator $generator The generator for the class
     */
    public function registerIdGenerator(string $className, IIdGenerator $generator);
}
