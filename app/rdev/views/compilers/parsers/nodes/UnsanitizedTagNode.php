<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines an unsanitized tag node
 */
namespace RDev\Views\Compilers\Parsers\Nodes;

class UnsanitizedTagNode extends Node
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
        return false;
    }

    /**
     * @inheritDoc
     */
    public function isUnsanitizedTag()
    {
        return true;
    }
}