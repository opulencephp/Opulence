<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Sessions\Handlers;

/**
 * Defines the array session handler, which is useful for testing
 */
class ArraySessionHandler extends SessionHandler
{
    /** @var array The local storage */
    private $storage = [];

    /**
     * @inheritdoc
     */
    public function close() : bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function destroy($sessionId) : bool
    {
        $this->storage = [];

        return true;
    }

    /**
     * @inheritdoc
     */
    public function gc($maxLifetime) : bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function open($savePath, $sessionId) : bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function doRead(string $sessionId) : string
    {
        if (array_key_exists($sessionId, $this->storage)) {
            return $this->storage[$sessionId];
        }

        return '';
    }

    /**
     * @inheritdoc
     */
    protected function doWrite(string $sessionId, string $sessionData) : bool
    {
        $this->storage[$sessionId] = $sessionData;

        return true;
    }
}
