<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Sessions\Handlers;

/**
 * Defines the file session handler
 */
class FileSessionHandler extends SessionHandler
{
    /** @var string The path to the session files */
    private string $path;

    /**
     * @param string $path The path to the session files
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @inheritdoc
     */
    public function close(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function destroy($sessionId): bool
    {
        @unlink("{$this->path}/$sessionId");

        return true;
    }

    /**
     * @inheritdoc
     */
    public function gc($maxLifetime): bool
    {
        $sessionFiles = glob($this->path . '/*', GLOB_NOSORT);
        $limit = time() - $maxLifetime;

        foreach ($sessionFiles as $sessionFile) {
            $lastModified = filemtime($sessionFile);
            if ($lastModified < $limit) {
                @unlink($sessionFile);
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function open($savePath, $sessionId): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function doRead(string $sessionId): string
    {
        if (file_exists("{$this->path}/$sessionId")) {
            return file_get_contents("{$this->path}/$sessionId");
        }

        return '';
    }

    /**
     * @inheritdoc
     */
    protected function doWrite(string $sessionId, string $sessionData): bool
    {
        return file_put_contents("{$this->path}/$sessionId", $sessionData, LOCK_EX) !== false;
    }
}
