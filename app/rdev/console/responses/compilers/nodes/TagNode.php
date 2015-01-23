<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines a tag node
 */
namespace RDev\Console\Responses\Compilers\Nodes;

class TagNode extends Node
{
    /**
     * {@inheritdoc}
     */
    public function isTag()
    {
        return true;
    }
}