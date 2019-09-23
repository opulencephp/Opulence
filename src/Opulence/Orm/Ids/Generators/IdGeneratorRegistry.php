<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Orm\Ids\Generators;

/**
 * Defines the Id generator registry
 */
final class IdGeneratorRegistry implements IIdGeneratorRegistry
{
    /** @var IIdGenerator[] The mapping of class names to their Id generators */
    private array $generators = [];

    /**
     * @inheritdoc
     */
    public function getIdGenerator(string $className): ?IIdGenerator
    {
        if (!isset($this->generators[$className])) {
            return null;
        }

        return $this->generators[$className];
    }

    /**
     * @inheritdoc
     */
    public function registerIdGenerator(string $className, IIdGenerator $generator): void
    {
        $this->generators[$className] = $generator;
    }
}
