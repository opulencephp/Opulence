<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Mocks a node for use in testing
 */
namespace RDev\Tests\Console\Responses\Compilers\Nodes\Mocks;
use RDev\Console\Responses\Compilers\Nodes;

class Node extends Nodes\Node
{
    /**
     * {@inheritdoc}
     */
    public function isTag()
    {
        return false;
    }
}