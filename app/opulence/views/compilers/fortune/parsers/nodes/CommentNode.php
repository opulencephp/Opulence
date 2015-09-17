<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a comment node
 */
namespace Opulence\Views\Compilers\Fortune\Parsers\Nodes;

class CommentNode extends Node
{
    /**
     * @inheritdoc
     */
    public function isComment()
    {
        return true;
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