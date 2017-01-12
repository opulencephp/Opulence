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
 * Defines a word node
 */
class WordNode extends Node
{
    /**
     * @inheritdoc
     */
    public function isTag() : bool
    {
        return false;
    }
}
