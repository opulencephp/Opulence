<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Http\Requests\Mocks;

use Opulence\Http\Requests\UploadedFile as BaseUploadedFile;

/**
 * Mocks the uploaded file
 */
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