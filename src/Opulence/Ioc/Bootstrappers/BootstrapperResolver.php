<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Bootstrappers;

use InvalidArgumentException;

/**
 * Defines the bootstrapper resolver
 */
class BootstrapperResolver implements IBootstrapperResolver
{
    /** @var array The list of bootstrapper classes to their instances */
    private $instances = [];

    /**
     * @inheritdoc
     */
    public function resolve(string $bootstrapperClass) : Bootstrapper
    {
        if (!isset($this->instances[$bootstrapperClass])) {
            $this->instances[$bootstrapperClass] = new $bootstrapperClass();
        }

        $bootstrapper = $this->instances[$bootstrapperClass];

        if (!$bootstrapper instanceof Bootstrapper) {
            throw new InvalidArgumentException("\"$bootstrapperClass\" does not extend Bootstrapper");
        }

        return $bootstrapper;
    }

    /**
     * @inheritdoc
     */
    public function resolveMany(array $bootstrapperClasses) : array
    {
        $resolvedBootstrappers = [];

        foreach ($bootstrapperClasses as $bootstrapperClass) {
            $resolvedBootstrappers[] = $this->resolve($bootstrapperClass);
        }

        return $resolvedBootstrappers;
    }
}
