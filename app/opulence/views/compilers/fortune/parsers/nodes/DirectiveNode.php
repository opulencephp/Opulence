<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a directive node
 */
namespace Opulence\Views\Compilers\Fortune\Parsers\Nodes;

class DirectiveNode extends Node
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
        return true;
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