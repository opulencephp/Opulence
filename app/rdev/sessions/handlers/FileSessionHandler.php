<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the file session handler
 */
namespace RDev\Sessions\Handlers;
use DateTime;
use RDev\Files\FileSystem;
use SessionHandlerInterface;

class FileSessionHandler implements SessionHandlerInterface
{
    /** @var FileSystem The file system to use to read/write files */
    private $fileSystem = null;
    /** @var string The path to the session files */
    private $path = "";

    /**
     * @param FileSystem $fileSystem The file system to use to read/write files
     * @param string $path The path to the session files
     */
    public function __construct(FileSystem $fileSystem, $path)
    {
        $this->fileSystem = $fileSystem;
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
        $this->fileSystem->deleteFile("{$this->path}/$sessionId");
    }

    /**
     * {@inheritdoc}
     */
    public function gc($maxLifetime)
    {
        $sessionFiles = $this->fileSystem->glob($this->path . "/*");

        foreach($sessionFiles as $sessionFile)
        {
            if(new DateTime("$maxLifetime seconds ago") > $this->fileSystem->getLastModified($sessionFile))
            {
                $this->fileSystem->deleteFile($sessionFile);
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
        if($this->fileSystem->exists("{$this->path}/$sessionId"))
        {
            return $this->fileSystem->read("{$this->path}/$sessionId");
        }

        return "";
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, $sessionData)
    {
        $this->fileSystem->write("{$this->path}/$sessionId", $sessionData, LOCK_EX);
    }
}