<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the random server selection strategy
 */
namespace Opulence\Databases\ConnectionPools\Strategies\ServerSelection;

use InvalidArgumentException;

class RandomServerSelectionStrategy implements IServerSelectionStrategy
{
    /**
     * @inheritDoc
     */
    public function select($servers)
    {
        if (!is_array($servers)) {
            $servers = [$servers];
        }

        if (count($servers) == 0) {
            throw new InvalidArgumentException("No servers specified");
        }

        return $servers[mt_rand(0, count($servers) - 1)];
    }
}