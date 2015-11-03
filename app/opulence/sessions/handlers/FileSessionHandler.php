<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Sessions\Handlers;

use DateTime;

/**
 * Defines the file session handler
 */
class FileSessionHandler extends SessionHandler
{
    /** @var string The path to the session files */
    private $path = "";

    /**
     * @param string $path The path to the session files
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @inheritdoc
     */
    public function close()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function destroy($sessionId)
    {
        @unlink("{$this->path}/$sessionId");
    }

    /**
     * @inheritdoc
     */
    public function gc($maxLifetime)
    {
        $sessionFiles = glob($this->path . "/*");

        foreach ($sessionFiles as $sessionFile) {
            $lastModified = DateTime::createFromFormat("U", filemtime($sessionFile));

            if (new DateTime("$maxLifetime seconds ago") > $lastModified) {
                @unlink($sessionFile);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function open($savePath, $sessionId)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function doRead($sessionId)
    {
        if (file_exists("{$this->path}/$sessionId")) {
            return file_get_contents("{$this->path}/$sessionId");
        }

        return "";
    }

    /**
     * @inheritdoc
     */
    protected function doWrite($sessionId, $sessionData)
    {
        file_put_contents("{$this->path}/$sessionId", $sessionData, LOCK_EX);
    }
}