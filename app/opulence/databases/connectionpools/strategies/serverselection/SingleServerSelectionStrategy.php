<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the single server selection strategy
 */
namespace Opulence\Databases\ConnectionPools\Strategies\ServerSelection;

use InvalidArgumentException;

class SingleServerSelectionStrategy implements IServerSelectionStrategy
{
    /**
     * @inheritdoc
     */
    public function select($servers)
    {
        if (!is_array($servers)) {
            $servers = [$servers];
        }

        if (count($servers) == 0) {
            throw new InvalidArgumentException("No servers specified");
        }

        return $servers[0];
    }
}