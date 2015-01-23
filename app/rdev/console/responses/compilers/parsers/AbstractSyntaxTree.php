<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines a response syntax tree
 */
namespace RDev\Console\Responses\Compilers\Parsers;
use RDev\Console\Responses\Compilers\Nodes;

class AbstractSyntaxTree
{
    /** @var Nodes\RootNode The root node */
    private $rootNode = null;
    /** @var Nodes\Node The current node */
    private $currentNode = null;

    public function __construct()
    {
        $this->rootNode = new Nodes\RootNode();
        $this->setCurrentNode($this->rootNode);
    }

    /**
     * Gets the current node
     *
     * @return Nodes\Node The current node
     */
    public function getCurrentNode()
    {
        return $this->currentNode;
    }

    /**
     * Gets the root node
     *
     * @return Nodes\RootNode The root node
     */
    public function getRootNode()
    {
        return $this->rootNode;
    }

    /**
     * Sets the current node
     *
     * @param Nodes\Node $node The node to set
     */
    public function setCurrentNode(Nodes\Node $node)
    {
        $this->currentNode = $node;
    }
}