<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the file session handler
 */
namespace RDev\Sessions\Handlers;
use DateTime;
use SessionHandlerInterface;

class FileSessionHandler implements SessionHandlerInterface
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
     * {@inheritdoc}
     */
    public function close()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
        @unlink("{$this->path}/$sessionId");
    }

    /**
     * {@inheritdoc}
     */
    public function gc($maxLifetime)
    {
        $sessionFiles = glob($this->path . "/*");

        foreach($sessionFiles as $sessionFile)
        {
            $lastModified = DateTime::createFromFormat("U", filemtime($sessionFile));

            if(new DateTime("$maxLifetime seconds ago") > $lastModified)
            {
                @unlink($sessionFile);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function open($savePath, $sessionId)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionId)
    {
        if(file_exists("{$this->path}/$sessionId"))
        {
            return file_get_contents("{$this->path}/$sessionId");
        }

        return "";
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, $sessionData)
    {
        file_put_contents("{$this->path}/$sessionId", $sessionData, LOCK_EX);
    }
}