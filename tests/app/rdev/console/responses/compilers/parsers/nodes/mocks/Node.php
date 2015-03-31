<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Mocks a node for use in testing
 */
namespace RDev\Tests\Console\Responses\Compilers\Parsers\Nodes\Mocks;
use RDev\Console\Responses\Compilers\Parsers\Nodes\Node as BaseNode;

class Node extends BaseNode
{
    /**
     * {@inheritdoc}
     */
    public function isTag()
    {
        return false;
    }
}