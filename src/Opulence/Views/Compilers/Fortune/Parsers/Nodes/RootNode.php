<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
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
    public function getParent() : Node
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isComment() : bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isDirective() : bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isDirectiveName() : bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isExpression() : bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isSanitizedTag() : bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isUnsanitizedTag() : bool
    {
        return false;
    }
}
