<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for server selection strategies to implement
 */
namespace Opulence\Databases\ConnectionPools\Strategies\ServerSelection;

use InvalidArgumentException;
use Opulence\Databases\Server;

interface IServerSelectionStrategy
{
    /**
     * Selects the server according to the strategy
     *
     * @param Server|Server[] $servers The server or list of servers to select from
     * @return Server The selected server
     * @throws InvalidArgumentException Thrown if a server could not be selected
     */
    public function select($servers);
}