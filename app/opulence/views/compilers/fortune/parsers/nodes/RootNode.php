<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the root node
 */
namespace Opulence\Views\Compilers\Fortune\Parsers\Nodes;

class RootNode extends Node
{
    public function __construct()
    {
        parent::__construct(null);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this;
    }

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
        return false;
    }
}