<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines a base sub-compiler
 */
namespace RDev\Views\Compilers\SubCompilers;
use RDev\Views\Compilers;

abstract class SubCompiler implements ISubCompiler
{
    /** @var Compilers\ICompiler The parent compiler */
    protected $parentCompiler = null;

    /**
     * @param Compilers\ICompiler $parentCompiler The parent compiler
     */
    public function __construct(Compilers\ICompiler $parentCompiler)
    {
        $this->parentCompiler = $parentCompiler;
    }
}