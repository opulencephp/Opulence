<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a word node
 */
namespace Opulence\Console\Responses\Compilers\Parsers\Nodes;

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