<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Bootstrappers\Caching;

use Opulence\Ioc\Bootstrappers\BootstrapperRegistry;
use Opulence\Ioc\Bootstrappers\IBootstrapperRegistry;

/**
 * Defines the bootstrapper file cache
 */
class FileCache implements ICache
{
    /** @var string The cache registry file path */
    private $filePath = '';
    /** @var int The expiration time for cached files */
    private $expirationTime = null;

    /**
     * @param string $filePath The cache registry file path
     * @param int|null $expirationTime The expiration time for cached files
     */
    public function __construct(string $filePath, int $expirationTime = null)
    {
        $this->filePath = $filePath;
        $this->expirationTime = $expirationTime;
    }

    /**
     * @inheritdoc
     */
    public function flush()
    {
        if (file_exists($this->filePath)) {
            @unlink($this->filePath);
        }
    }

    /**
     * @inheritdoc
     */
    public function get()
    {
        if (!file_exists($this->filePath)) {
            return null;
        }

        // Perform garbage collection if this file has expired
        if ($this->expirationTime !== null && filemtime($this->filePath) < $this->expirationTime) {
            $this->flush();

            return null;
        }

        $rawContents = file_get_contents($this->filePath);
        $decodedContents = json_decode($rawContents, true);
        $registry = new BootstrapperRegistry();

        foreach ($decodedContents['eager'] as $eagerBootstrapperClass) {
            $registry->registerEagerBootstrapper($eagerBootstrapperClass);
        }

        foreach ($decodedContents['lazy'] as $boundClass => $bindingData) {
            if ($bindingData['target'] === null) {
                $registry->registerLazyBootstrapper([$boundClass], $bindingData['bootstrapper']);
            } else {
                $registry->registerLazyBootstrapper(
                    [[$boundClass => $bindingData['target']]],
                    $bindingData['bootstrapper']
                );
            }
        }

        return $registry;
    }

    /**
     * @inheritdoc
     */
    public function set(IBootstrapperRegistry $registry)
    {
        $data = [
            'eager' => $registry->getEagerBootstrappers(),
            'lazy' => []
        ];

        foreach ($registry->getLazyBootstrapperBindings() as $boundClass => $bindingData) {
            $data['lazy'][$boundClass] = [
                'bootstrapper' => $bindingData['bootstrapper'],
                'target' => $bindingData['target']
            ];
        }

        file_put_contents($this->filePath, json_encode($data));
    }
}
