<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Views\Compilers\Fortune\Parsers\Nodes;

/**
 * Defines a directive name node
 */
class DirectiveNameNode extends Node
{
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
        return true;
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
