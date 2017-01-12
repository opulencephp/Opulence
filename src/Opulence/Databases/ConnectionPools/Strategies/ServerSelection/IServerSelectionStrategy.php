<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Databases\ConnectionPools\Strategies\ServerSelection;

use InvalidArgumentException;
use Opulence\Databases\Server;

/**
 * Defines the interface for server selection strategies to implement
 */
interface IServerSelectionStrategy
{
    /**
     * Selects the server according to the strategy
     *
     * @param Server|Server[] $servers The server or list of servers to select from
     * @return Server The selected server
     * @throws InvalidArgumentException Thrown if a server could not be selected
     */
    public function select($servers) : Server;
}
