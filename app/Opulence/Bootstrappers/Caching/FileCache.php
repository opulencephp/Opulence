<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Bootstrappers\Caching;

use Opulence\Bootstrappers\IBootstrapperRegistry;

/**
 * Defines the bootstrapper file cache
 */
class FileCache implements ICache
{
    /**
     * @inheritdoc
     */
    public function flush($filePath)
    {
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }

    /**
     * @inheritdoc
     */
    public function get($filePath, IBootstrapperRegistry &$registry)
    {
        if (file_exists($filePath)) {
            $this->loadRegistryFromCache($filePath, $registry);
        } else {
            $registry->setBootstrapperDetails();
            // Write this for next time
            $this->set($filePath, $registry);
        }
    }

    /**
     * @inheritdoc
     */
    public function set($filePath, IBootstrapperRegistry $registry)
    {
        $data = [
            "eager" => $registry->getEagerBootstrappers(),
            "lazy" => []
        ];

        foreach ($registry->getLazyBootstrapperBindings() as $boundClass => $bootstrapperClass) {
            $data["lazy"][$boundClass] = $bootstrapperClass;
        }

        file_put_contents($filePath, json_encode($data));
    }

    /**
     * Loads a cached registry file's data into a registry
     *
     * @param string $filePath The cache registry file path
     * @param IBootstrapperRegistry $registry The registry to read settings into
     */
    protected function loadRegistryFromCache($filePath, IBootstrapperRegistry &$registry)
    {
        $rawContents = file_get_contents($filePath);
        $decodedContents = json_decode($rawContents, true);

        foreach ($decodedContents["eager"] as $eagerBootstrapperClass) {
            $registry->registerEagerBootstrapper($eagerBootstrapperClass);
        }

        foreach ($decodedContents["lazy"] as $boundClass => $lazyBootstrapperClass) {
            $registry->registerLazyBootstrapper($boundClass, $lazyBootstrapperClass);
        }
    }
}