<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Views\Compilers\Fortune\Parsers\Nodes;

/**
 * Defines the root node
 */
class RootNode extends Node
{
    public function __construct()
    {
        parent::__construct(null);
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isComment()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isDirective()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isDirectiveName()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isExpression()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isSanitizedTag()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isUnsanitizedTag()
    {
        return false;
    }
}