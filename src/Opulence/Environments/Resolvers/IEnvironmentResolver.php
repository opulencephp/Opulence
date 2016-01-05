<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Environments\Resolvers;

use Opulence\Environments\Hosts\IHost;

/**
 * Defines the interface for environment resolvers to implement
 */
interface IEnvironmentResolver
{
    /**
     * Registers a host for a particular environment name
     *
     * @param string $environmentName The name of the environment this host belongs to
     * @param IHost|IHost[] $hosts The host or hosts to add
     */
    public function registerHost($environmentName, $hosts);

    /**
     * Gets the environment the server belongs to, eg "production"
     *
     * @param string $hostName The host name to resolve
     * @return string The environment the server belongs to
     */
    public function resolve($hostName);
}