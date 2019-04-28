<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Console\Tests\Commands\Mocks;

use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;

/**
 * Defines a command without a name
 */
class NamelessCommand extends Command
{
    /**
     * @inheritdoc
     */
    protected function define(): void
    {
        // Don't do anything
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        $response->write('foo');
    }
}
