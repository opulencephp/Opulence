<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a directive name node
 */
namespace Opulence\Views\Compilers\Fortune\Parsers\Nodes;

class DirectiveNameNode extends Node
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
        return true;
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