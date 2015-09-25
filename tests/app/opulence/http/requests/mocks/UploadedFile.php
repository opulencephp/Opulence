<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks the uploaded file
 */
namespace Opulence\Tests\HTTP\Requests\Mocks;
use Opulence\HTTP\Requests\UploadedFile as BaseUploadedFile;

class UploadedFile extends BaseUploadedFile
{
    /**
     * @inheritDoc
     */
    protected function doMove($source, $target)
    {
        return copy($source, $target);
    }
}