<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a tag node
 */
namespace Opulence\Console\Responses\Compilers\Parsers\Nodes;

class TagNode extends Node
{
    /**
     * @inheritdoc
     */
    public function isTag()
    {
        return true;
    }
}