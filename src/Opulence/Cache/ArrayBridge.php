<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Cache;

/**
 * Defines the array bridge
 */
class ArrayBridge implements ICacheBridge
{
    /** @var array The storage */
    private array $storage = [];

    /**
     * @inheritdoc
     */
    public function decrement(string $key, int $by = 1): int
    {
        $this->storage[$key] -= $by;

        return $this->storage[$key];
    }

    /**
     * @inheritdoc
     */
    public function delete(string $key): void
    {
        unset($this->storage[$key]);
    }

    /**
     * @inheritdoc
     */
    public function flush(): void
    {
        $this->storage = [];
    }

    /**
     * @inheritdoc
     */
    public function get(string $key)
    {
        if (array_key_exists($key, $this->storage)) {
            return $this->storage[$key];
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->storage);
    }

    /**
     * @inheritdoc
     */
    public function increment(string $key, int $by = 1): int
    {
        $this->storage[$key] += $by;

        return $this->storage[$key];
    }

    /**
     * @inheritdoc
     */
    public function set(string $key, $value, int $lifetime): void
    {
        if ($lifetime > 0) {
            $this->storage[$key] = $value;
        }
    }
}
