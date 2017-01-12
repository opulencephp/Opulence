<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Tests\Databases\Mocks;

use Opulence\Databases\Server as BaseServer;

/**
 * Mocks the server class for use in testing
 */
class Server extends BaseServer
{
    protected $host = '1.2.3.4';
    protected $username = 'foo';
    protected $password = 'bar';
    protected $databaseName = 'fakedatabase';
}
