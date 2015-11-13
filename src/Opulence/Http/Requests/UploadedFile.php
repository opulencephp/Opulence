<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Http\Requests;

use finfo;
use SplFileInfo;

/**
 * Defines an uploaded file
 */
class UploadedFile extends SplFileInfo
{
    /** @var string The temporary name of the file */
    private $tmpFilename = "";
    /** @var int The size of the file in bytes */
    private $tmpSize = 0;
    /** @var string The mime type of the file */
    private $tmpMimeType = "";
    /** @var int The error message, if there was any */
    private $error = UPLOAD_ERR_OK;

    /**
     * @param string $path The path to the file
     * @param string $tmpFilename The temporary filename
     * @param int $tmpSize The size of the temporary file in bytes
     * @param string $tmpMimeType The mime type of the temporary file
     * @param int $error The error message, if there was any
     */
    public function __construct($path, $tmpFilename, $tmpSize, $tmpMimeType = "", $error = UPLOAD_ERR_OK)
    {
        parent::__construct($path);

        $this->tmpFilename = $tmpFilename;
        $this->tmpSize = $tmpSize;
        $this->tmpMimeType = $tmpMimeType;
        $this->error = $error;
    }

    /**
     * @return int
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Gets the actual mime type of the file
     *
     * @return string The actual mime type
     */
    public function getMimeType()
    {
        $fInfo = new finfo(FILEINFO_MIME_TYPE);

        return $fInfo->file($this->getPathname());
    }

    /**
     * Gets the temporary file's extension
     *
     * @return string The temporary file's extension
     */
    public function getTempExtension()
    {
        return pathinfo($this->tmpFilename, PATHINFO_EXTENSION);
    }

    /**
     * @return string
     */
    public function getTempFilename()
    {
        return $this->tmpFilename;
    }

    /**
     * @return string
     */
    public function getTempMimeType()
    {
        return $this->tmpMimeType;
    }

    /**
     * @return int
     */
    public function getTempSize()
    {
        return $this->tmpSize;
    }

    /**
     * Gets whether or not this file has errors
     *
     * @return bool True if the file has errors, otherwise false
     */
    public function hasErrors()
    {
        return $this->error !== UPLOAD_ERR_OK;
    }

    /**
     * Moves the file to the target path
     *
     * @param string $targetDirectory The target directory
     * @param string|null $name The new name
     * @throws UploadException Thrown if the file could not be moved
     */
    public function move($targetDirectory, $name = null)
    {
        if ($this->hasErrors()) {
            throw new UploadException("Cannot move file with errors");
        }

        if (!is_dir($targetDirectory)) {
            if (!mkdir($targetDirectory, 0777, true)) {
                throw new UploadException("Could not create directory " . $targetDirectory);
            }
        } elseif (!is_writable($targetDirectory)) {
            throw new UploadException($targetDirectory . " is not writable");
        }

        $name = $name ?: $this->getBasename();
        $targetPath = rtrim($targetDirectory, "\\/") . "/" . $name;

        if (!$this->doMove($this->getPathname(), $targetPath)) {
            throw new UploadException("Could not move the uploaded file");
        }
    }

    /**
     * Moves a file from one location to another
     * This is split into its own method so that it can be overridden for testing purposes
     *
     * @param string $source The path to move from
     * @param string $target The path to move to
     * @return bool True if the move was successful, otherwise false
     */
    protected function doMove($source, $target)
    {
        return @move_uploaded_file($source, $target);
    }
}