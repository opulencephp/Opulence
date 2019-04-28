<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Ioc;

/**
 * Defines an instance container binding
 * @internal
 */
class InstanceContainerBinding implements IContainerBinding
{
    /** @var object The instance */
    private $instance;

    /**
     * @param object $instance The instance
     */
    public function __construct($instance)
    {
        $this->instance = $instance;
    }

    /**
     * @return object
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @return bool
     */
    public function resolveAsSingleton(): bool
    {
        return true;
    }
}
