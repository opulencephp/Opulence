<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
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
     * @inheritDoc
     */
    public function getIdGenerator($className)
    {
        if (!isset($this->generators[$className])) {
            return null;
        }

        return $this->generators[$className];
    }

    /**
     * @inheritDoc
     */
    public function registerIdGenerator($className, IIdGenerator $generator)
    {
        $this->generators[$className] = $generator;
    }
}