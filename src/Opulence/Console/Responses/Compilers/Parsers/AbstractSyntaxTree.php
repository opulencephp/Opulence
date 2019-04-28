<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Console\Responses\Compilers\Parsers;

use Opulence\Console\Responses\Compilers\Parsers\Nodes\Node;
use Opulence\Console\Responses\Compilers\Parsers\Nodes\RootNode;

/**
 * Defines a response syntax tree
 */
class AbstractSyntaxTree
{
    /** @var RootNode The root node */
    private $rootNode;
    /** @var Node The current node */
    private $currentNode;

    public function __construct()
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
    public function getRootNode(): Node
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
