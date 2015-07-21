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
     * @inheritDoc
     */
    public function isDirective()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function isDirectiveName()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function isExpression()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function isSanitizedTag()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isUnsanitizedTag()
    {
        return false;
    }
}