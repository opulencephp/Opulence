<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Databases\TestsTemp\Mocks;

use Opulence\Databases\Server as BaseServer;

/**
 * Mocks the server class for use in testing
 */
class Server extends BaseServer
{
    protected string $host = '1.2.3.4';
    protected string $username = 'foo';
    protected string $password = 'bar';
    protected string $databaseName = 'fakedatabase';
}
