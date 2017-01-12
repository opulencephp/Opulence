<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Console\Responses\Compilers\Parsers\Nodes;

/**
 * Defines a tag node
 */
class TagNode extends Node
{
    /**
     * @inheritdoc
     */
    public function isTag() : bool
    {
        return true;
    }
}
