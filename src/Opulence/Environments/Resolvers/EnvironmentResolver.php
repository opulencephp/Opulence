<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Environments\Resolvers;

use Opulence\Environments\Environment;
use Opulence\Environments\Hosts\HostRegex;
use Opulence\Environments\Hosts\IHost;

/**
 * Defines the environment resolver
 */
class EnvironmentResolver implements IEnvironmentResolver
{
    /** @var array The environment names to hosts */
    private $environmentsToHosts = [];

    /**
     * @inheritdoc
     */
    public function registerHost(string $environmentName, $hosts)
    {
        if (!isset($this->environmentsToHosts[$environmentName])) {
            $this->environmentsToHosts[$environmentName] = [];
        }

        if (!is_array($hosts)) {
            $hosts = [$hosts];
        }

        foreach ($hosts as $host) {
            $this->environmentsToHosts[$environmentName][] = $host;
        }
    }

    /**
     * @inheritdoc
     */
    public function resolve(string $hostName) : string
    {
        foreach ($this->environmentsToHosts as $environmentName => $hosts) {
            /** @var IHost $host */
            foreach ($hosts as $host) {
                if ($host instanceof HostRegex) {
                    if (preg_match($host->getValue(), $hostName) === 1) {
                        return $environmentName;
                    }
                } elseif ($host->getValue() === $hostName) {
                    return $environmentName;
                }
            }
        }

        // Default to production
        return Environment::PRODUCTION;
    }
} 