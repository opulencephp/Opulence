<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Http\Tests\Requests\Mocks;

use Opulence\Http\Requests\UploadedFile as BaseUploadedFile;

/**
 * Mocks the uploaded file
 */
class UploadedFile extends BaseUploadedFile
{
    /**
     * @inheritDoc
     */
    protected function doMove(string $source, string $target): bool
    {
        return copy($source, $target);
    }
}
