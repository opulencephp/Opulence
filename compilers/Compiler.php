<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines methods for compiling views
 */
namespace Opulence\Views\Compilers;
use Opulence\Views\Caching\ICache;
use Opulence\Views\IView;

class Compiler implements ICompiler
{
    /** @var ICompilerRegistry The compiler registry */
    protected $registry = null;
    /** @var ICache The view cache */
    protected $cache = null;

    /**
     * @param ICompilerRegistry $registry The compiler registry
     * @param ICache $cache The view cache
     */
    public function __construct(ICompilerRegistry $registry, ICache $cache)
    {
        $this->registry = $registry;
        $this->cache = $cache;
    }

    /**
     * @inheritdoc
     */
    public function compile(IView $view, $contents = null)
    {
        if($contents === null)
        {
            $contents = $view->getContents();
        }

        $varsBeforeCompiling = $view->getVars();

        if($this->cache->has($contents, $varsBeforeCompiling))
        {
            return $this->cache->get($contents, $varsBeforeCompiling);
        }

        $compiledContents = $this->registry->get($view)->compile($view, $contents);
        $this->cache->set($compiledContents, $contents, $varsBeforeCompiling);

        return $compiledContents;
    }
} 