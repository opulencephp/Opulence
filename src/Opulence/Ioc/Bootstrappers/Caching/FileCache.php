<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Ioc\Bootstrappers\Caching;

use Opulence\Ioc\Bootstrappers\IBootstrapperRegistry;

/**
 * Defines the bootstrapper file cache
 */
class FileCache implements ICache
{
    /**
     * @inheritdoc
     */
    public function flush(string $filePath)
    {
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }

    /**
     * @inheritdoc
     */
    public function get(string $filePath, IBootstrapperRegistry &$registry)
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
    public function set(string $filePath, IBootstrapperRegistry $registry)
    {
        $data = [
            "eager" => $registry->getEagerBootstrappers(),
            "lazy" => []
        ];

        foreach ($registry->getLazyBootstrapperBindings() as $boundClass => $bindingData) {
            $data["lazy"][$boundClass] = [
                "bootstrapper" => $bindingData["bootstrapper"],
                "target" => $bindingData["target"]
            ];
        }

        file_put_contents($filePath, json_encode($data));
    }

    /**
     * Loads a cached registry file's data into a registry
     *
     * @param string $filePath The cache registry file path
     * @param IBootstrapperRegistry $registry The registry to read settings into
     */
    protected function loadRegistryFromCache(string $filePath, IBootstrapperRegistry &$registry)
    {
        $rawContents = file_get_contents($filePath);
        $decodedContents = json_decode($rawContents, true);

        foreach ($decodedContents["eager"] as $eagerBootstrapperClass) {
            $registry->registerEagerBootstrapper($eagerBootstrapperClass);
        }

        foreach ($decodedContents["lazy"] as $boundClass => $bindingData) {
            if ($bindingData["target"] === null) {
                $registry->registerLazyBootstrapper([$boundClass], $bindingData["bootstrapper"]);
            } else {
                $registry->registerLazyBootstrapper(
                    [[$boundClass => $bindingData["target"]]],
                    $bindingData["bootstrapper"]
                );
            }
        }
    }
}