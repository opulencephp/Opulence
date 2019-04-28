<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Console\Tests\Responses\Mocks;

use Opulence\Console\Responses\Response as BaseResponse;

/**
 * Mocks the console response for use in tests
 */
class Response extends BaseResponse
{
    /**
     * Clears the response buffer
     */
    public function clear(): void
    {
        $this->write(chr(27) . '[2J' . chr(27) . '[;H');
    }

    /**
     * @inheritdoc
     */
    protected function doWrite(string $message, bool $includeNewLine): void
    {
        echo $message . ($includeNewLine ? PHP_EOL : '');
    }
}
