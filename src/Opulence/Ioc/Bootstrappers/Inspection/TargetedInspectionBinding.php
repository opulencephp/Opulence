<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Ioc\Bootstrappers\Inspection;

use Opulence\Ioc\Bootstrappers\Bootstrapper;

/**
 * Defines a targeted binding
 */
final class TargetedInspectionBinding extends InspectionBinding
{
    /** @var string The targeted class */
    private $targetClass;

    /**
     * @param string $targetClass The targeted class
     * @param string $interface The interface that is bound
     * @param Bootstrapper $bootstrapper The bootstrapper that registered the binding
     */
    public function __construct(string $targetClass, string $interface, Bootstrapper $bootstrapper)
    {
        parent::__construct($interface, $bootstrapper);

        $this->targetClass = $targetClass;
    }

    /**
     * Gets the target class
     *
     * @return string The target class
     */
    public function getTargetClass(): string
    {
        return $this->targetClass;
    }
}
