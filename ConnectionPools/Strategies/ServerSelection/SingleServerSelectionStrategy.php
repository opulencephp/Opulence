<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Databases\ConnectionPools\Strategies\ServerSelection;

use InvalidArgumentException;

/**
 * Defines the single server selection strategy
 */
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