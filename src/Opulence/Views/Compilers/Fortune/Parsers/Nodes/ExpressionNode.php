<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Views\Compilers\Fortune\Parsers\Nodes;

/**
 * Defines an expression node
 */
class ExpressionNode extends Node
{
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
        return true;
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