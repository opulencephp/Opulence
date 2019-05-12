<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Ioc\Bootstrappers\Inspection\Caching;

/**
 * Defines the bootstrapper binding cache that uses a file as the cache
 */
final class FileBootstrapperBindingCache implements IBootstrapperBindingCache
{
    /** @var string The cache file path */
    private $filePath;

    /**
     * @param string $filePath The cache file path
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @inheritdoc
     */
    public function flush(): void
    {
        if (\file_exists($this->filePath)) {
            @unlink($this->filePath);
        }
    }

    /**
     * @inheritdoc
     */
    public function get(): ?array
    {
        $rawContents = @\file_get_contents($this->filePath);

        if ($rawContents === false) {
            return null;
        }

        return \unserialize(\base64_decode($rawContents));
    }

    /**
     * @inheritdoc
     */
    public function set(array $bindings): void
    {
        \file_put_contents($this->filePath, \base64_encode(\serialize($bindings)));
    }
}
