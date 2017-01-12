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
 * Defines the Id generator registry
 */
class IdGeneratorRegistry implements IIdGeneratorRegistry
{
    /** @var IIdGenerator[] The mapping of class names to their Id generators */
    private $generators = [];

    /**
     * @inheritdoc
     */
    public function getIdGenerator(string $className)
    {
        if (!isset($this->generators[$className])) {
            return null;
        }

        return $this->generators[$className];
    }

    /**
     * @inheritdoc
     */
    public function registerIdGenerator(string $className, IIdGenerator $generator)
    {
        $this->generators[$className] = $generator;
    }
}
