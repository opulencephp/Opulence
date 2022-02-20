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
 * Defines the file session handler
 */
class FileSessionHandler extends SessionHandler
{
    /** @var string The path to the session files */
    private $path = '';

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
    public function close() : bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function destroy($id) : bool
    {
        @unlink("{$this->path}/$id");

        return true;
    }

    /**
     * @inheritdoc
     */
    public function gc($max_lifetime) : int
    {
        $sessionFiles = glob($this->path . '/*', GLOB_NOSORT);
        $limit = time() - $max_lifetime;
        $numDeletedSessions = 0;

        foreach ($sessionFiles as $sessionFile) {
            $lastModified = filemtime($sessionFile);
            if ($lastModified < $limit) {
                @unlink($sessionFile);
                $numDeletedSessions++;
            }
        }

        return $numDeletedSessions;
    }

    /**
     * @inheritdoc
     */
    public function open($path, $name) : bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function doRead(string $sessionId) : string
    {
        if (file_exists("{$this->path}/$sessionId")) {
            return file_get_contents("{$this->path}/$sessionId");
        }

        return '';
    }

    /**
     * @inheritdoc
     */
    protected function doWrite(string $sessionId, string $sessionData) : bool
    {
        return file_put_contents("{$this->path}/$sessionId", $sessionData, LOCK_EX) !== false;
    }
}
