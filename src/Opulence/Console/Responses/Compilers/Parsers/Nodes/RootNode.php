<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Console\Responses\Compilers\Parsers\Nodes;

/**
 * Defines a root node
 */
class RootNode extends Node
{
    public function __construct()
    {
        parent::__construct(null);
    }

    /**
     * @inheritdoc
     */
    public function getParent(): Node
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isTag(): bool
    {
        return false;
    }
}
