<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
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
    public function isTag()
    {
        return false;
    }
}