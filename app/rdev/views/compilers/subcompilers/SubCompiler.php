<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines a base sub-compiler
 */
namespace RDev\Views\Compilers\SubCompilers;
use RDev\Views\Compilers\ICompiler;

abstract class SubCompiler implements ISubCompiler
{
    /** @var ICompiler The parent compiler */
    protected $parentCompiler = null;

    /**
     * @param ICompiler $parentCompiler The parent compiler
     */
    public function __construct(ICompiler $parentCompiler)
    {
        $this->parentCompiler = $parentCompiler;
    }
}