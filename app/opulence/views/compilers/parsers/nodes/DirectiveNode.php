<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a directive node
 */
namespace Opulence\Views\Compilers\Parsers\Nodes;

class DirectiveNode extends Node
{
    /**
     * @inheritDoc
     */
    public function isDirective()
    {
        return true;
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
        return false;
    }

    /**
     * @inheritDoc
     */
    public function isUnsanitizedTag()
    {
        return false;
    }
}