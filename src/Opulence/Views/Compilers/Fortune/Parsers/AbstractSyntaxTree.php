<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Views\Compilers\Fortune\Parsers;

use Opulence\Views\Compilers\Fortune\Parsers\Nodes\Node;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\RootNode;

/**
 * Defines a view abstract syntax tree
 */
class AbstractSyntaxTree
{
    /** @var RootNode The root node */
    private RootNode $rootNode;
    /** @var Node The current node */
    private Node $currentNode;

    public function __construct()
    {
        $this->clearNodes();
    }

    /**
     * Clears all the non-root nodes
     */
    public function clearNodes(): void
    {
        $this->rootNode = new RootNode();
        $this->setCurrentNode($this->rootNode);
    }

    /**
     * Gets the current node
     *
     * @return Node The current node
     */
    public function getCurrentNode(): Node
    {
        return $this->currentNode;
    }

    /**
     * Gets the root node
     *
     * @return RootNode The root node
     */
    public function getRootNode(): RootNode
    {
        return $this->rootNode;
    }

    /**
     * Sets the current node
     *
     * @param Node $node The node to set
     * @return Node The current node
     */
    public function setCurrentNode(Node $node): Node
    {
        $this->currentNode = $node;

        return $this->currentNode;
    }
}
