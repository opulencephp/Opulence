<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a sanitized tag node
 */
namespace Opulence\Views\Compilers\Fortune\Parsers\Nodes;

class SanitizedTagNode extends Node
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
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isSanitizedTag()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isUnsanitizedTag()
    {
        return false;
    }
}