<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Views\Compilers;

use InvalidArgumentException;
use Opulence\Views\IView;

/**
 * Defines the compiler dispatcher
 */
class CompilerRegistry implements ICompilerRegistry
{
    /** @var ICompiler[] The mapping of extensions to compiler instances */
    protected $compilers = [];

    /**
     * @inheritdoc
     */
    public function getCompiler(IView $view) : ICompiler
    {
        $extension = $this->getExtension($view);

        if (!isset($this->compilers[$extension])) {
            throw new InvalidArgumentException("No compiler registered for extension $extension");
        }

        return $this->compilers[$extension];
    }

    /**
     * @inheritdoc
     */
    public function registerCompiler(string $extension, ICompiler $compiler)
    {
        $this->compilers[$extension] = $compiler;
    }

    /**
     * Gets the extension for a view
     *
     * @param IView $view The view whose extension we're getting
     * @return string The view's extension
     * @throws InvalidArgumentException Thrown if no extension was found
     */
    protected function getExtension(IView $view) : string
    {
        // Find a registered extension that the view's path ends with
        foreach (array_keys($this->compilers) as $extension) {
            $lengthDifference = strlen($view->getPath()) - strlen($extension);

            if ($lengthDifference >= 0 && strpos($view->getPath(), $extension, $lengthDifference) !== false) {
                return $extension;
            }
        }

        throw new InvalidArgumentException("No extension registered for path \"{$view->getPath()}\"");
    }
}