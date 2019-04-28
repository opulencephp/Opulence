<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Databases\Tests\Adapters\Pdo\Mocks;

use Opulence\Databases\Adapters\Pdo\Driver as BaseDriver;
use Opulence\Databases\Providers\Provider;
use Opulence\Databases\Server;

/**
 * Mocks the PDO driver for use in testing
 */
class Driver extends BaseDriver
{
    /**
     * @inheritdoc
     */
    protected function getDsn(Server $server, array $options = []): string
    {
        return 'fakedsn';
    }

    /**
     * @inheritdoc
     */
    protected function setProvider(): void
    {
        $this->provider = new Provider();
    }
}
