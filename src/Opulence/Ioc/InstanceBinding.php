<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Ioc;

/**
 * Defines an instance binding
 */
class InstanceBinding implements IBinding
{
    /** @var object The instance */
    private $instance = null;

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
    public function resolveAsSingleton() : bool
    {
        return true;
    }
}
