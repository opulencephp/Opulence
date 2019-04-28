<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Console\Responses\Compilers\Parsers\Nodes;

/**
 * Defines a response node
 */
abstract class Node
{
    /** @var mixed|null The value of the node */
    protected $value;
    /** @var Node|null The parent node */
    protected $parent;
    /** @var Node[] The child nodes */
    protected $children = [];

    /**
     * @param mixed $value The value of the node
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }

    /**
     * Gets whether or not this is a tag node
     *
     * @return bool True if this is a tag node, otherwise false
     */
    abstract public function isTag(): bool;

    /**
     * Adds a child to this node
     *
     * @param Node $node The child to add
     * @return self Returns this for chaining
     */
    public function addChild(Node $node): self
    {
        $node->setParent($this);
        $this->children[] = $node;

        return $this;
    }

    /**
     * Gets the list of children of this node
     *
     * @return Node[] The list of children
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * Gets the parent node
     *
     * @return Node The parent node
     */
    public function getParent(): Node
    {
        return $this->parent;
    }

    /**
     * Gets the value of this node
     *
     * @return mixed|null The value of this node if there is one, otherwise null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Gets whether or not this node is a leaf
     *
     * @return bool True if this is a leaf, otherwise false
     */
    public function isLeaf(): bool
    {
        return count($this->children) === 0;
    }

    /**
     * Gets whether or not this node is the root
     *
     * @return bool True if this is a root node, otherwise false
     */
    public function isRoot(): bool
    {
        return $this->parent == null;
    }

    /**
     * @param Node|null $parent
     */
    public function setParent(?Node $parent): void
    {
        $this->parent = $parent;
    }
}
