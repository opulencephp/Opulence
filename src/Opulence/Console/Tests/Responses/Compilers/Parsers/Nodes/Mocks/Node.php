<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Console\Tests\Responses\Compilers\Parsers\Nodes\Mocks;

use Opulence\Console\Responses\Compilers\Parsers\Nodes\Node as BaseNode;

/**
 * Mocks a node for use in testing
 */
class Node extends BaseNode
{
    /**
     * @inheritdoc
     */
    public function isTag(): bool
    {
        return false;
    }
}
