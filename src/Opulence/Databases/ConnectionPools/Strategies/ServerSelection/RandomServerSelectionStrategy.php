<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Databases\ConnectionPools\Strategies\ServerSelection;

use InvalidArgumentException;
use Opulence\Databases\Server;

/**
 * Defines the random server selection strategy
 */
class RandomServerSelectionStrategy implements IServerSelectionStrategy
{
    /**
     * @inheritdoc
     */
    public function select($servers): Server
    {
        if (!is_array($servers)) {
            $servers = [$servers];
        }

        if (count($servers) === 0) {
            throw new InvalidArgumentException('No servers specified');
        }

        return $servers[random_int(0, count($servers) - 1)];
    }
}
