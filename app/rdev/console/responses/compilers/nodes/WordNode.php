<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a word node
 */
namespace RDev\Console\Responses\Compilers\Nodes;

class WordNode extends Node
{
    /**
     * {@inheritdoc}
     */
    public function isTag()
    {
        return false;
    }
}