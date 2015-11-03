<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Views\Compilers\Fortune;

/**
 * Defines the Fortune directive transpiler registrant
 */
class DirectiveTranspilerRegistrant
{
    /**
     * Registers the Fortune directive transpilers
     *
     * @param ITranspiler $transpiler The transpiler to register to
     */
    public function registerDirectiveTranspilers(ITranspiler &$transpiler)
    {
        $transpiler->registerDirectiveTranspiler("else", function () {
            return '<?php else: ?>';
        });
        $transpiler->registerDirectiveTranspiler("elseif", function ($expression) {
            return '<?php elseif' . $expression . ': ?>';
        });
        $transpiler->registerDirectiveTranspiler("forelse", function () {
            return '<?php endforeach; if(array_pop($__opulenceForElseEmpty)): ?>';
        });
        $transpiler->registerDirectiveTranspiler("endforeach", function () {
            return '<?php endforeach; ?>';
        });
        $transpiler->registerDirectiveTranspiler("endif", function () {
            return '<?php endif; ?>';
        });
        $transpiler->registerDirectiveTranspiler("endfor", function () {
            return '<?php endfor; ?>';
        });
        $transpiler->registerDirectiveTranspiler("endpart", function () {
            return '<?php $__opulenceFortuneTranspiler->endPart(); ?>';
        });
        $transpiler->registerDirectiveTranspiler("endwhile", function () {
            return '<?php endwhile; ?>';
        });
        $transpiler->registerDirectiveTranspiler("extends", function ($expression) use ($transpiler) {
            // Create the parent
            $code = '$__opulenceViewParent = $__opulenceViewFactory->create' . $expression . ';';
            $code .= '$__opulenceFortuneTranspiler->addParent($__opulenceViewParent, $__opulenceView);';
            $code .= 'extract($__opulenceView->getVars());';
            $transpiler->prepend('<?php ' . $code . ' ?>');

            // Compile the parent, keep track of the contents, and echo them in the appended text
            $code = '$__opulenceParentContents = isset($__opulenceParentContents) ? $__opulenceParentContents : [];';
            $code .= '$__opulenceParentContents[] = $__opulenceFortuneTranspiler->transpile($__opulenceViewParent);';
            $transpiler->prepend('<?php ' . $code . ' ?>');

            // Echo the contents at the end of the content
            $code = 'echo eval("?>" . array_shift($__opulenceParentContents));';
            $transpiler->append('<?php ' . $code . ' ?>');

            return "";
        });
        $transpiler->registerDirectiveTranspiler("for", function ($expression) {
            return '<?php for' . $expression . ': ?>';
        });
        $transpiler->registerDirectiveTranspiler("foreach", function ($expression) {
            return '<?php foreach' . $expression . ': ?>';
        });
        $transpiler->registerDirectiveTranspiler("forif", function ($expression) {
            $code = '<?php if(!isset($__opulenceForElseEmpty)): $__opulenceForElseEmpty = []; endif;';
            $code .= '$__opulenceForElseEmpty[] = true;';
            $code .= 'foreach' . $expression . ':';
            $code .= '$__opulenceForElseEmpty[count($__opulenceForElseEmpty) - 1] = false; ?>';

            return $code;
        });
        $transpiler->registerDirectiveTranspiler("if", function ($expression) {
            return '<?php if' . $expression . ': ?>';
        });
        $transpiler->registerDirectiveTranspiler("include", function ($expression) {
            // Check if a list of variables were passed in as a second parameter
            if (preg_match("/^\((('|\")(.*)\\2),\s*(.+)\)$/", $expression, $matches) === 1) {
                $code = '<?php $__opulenceIncludedView = $__opulenceViewFactory->create(' . $matches[1] . ');';
                $code .= '$__opulenceIncludedView->setVars(' . $matches[4] . ');';
            } else {
                $code = '<?php $__opulenceIncludedView = $__opulenceViewFactory->create' . $expression . ';';
            }

            $code .= 'eval("?>" . $__opulenceFortuneTranspiler->transpile($__opulenceIncludedView)); ?>';

            return $code;
        });
        $transpiler->registerDirectiveTranspiler("parent", function () {
            // This placeholder will be overwritten later
            return "__opulenceParentPlaceholder";
        });
        $transpiler->registerDirectiveTranspiler("part", function ($expression) {
            return '<?php $__opulenceFortuneTranspiler->startPart' . $expression . '; ?>';
        });
        $transpiler->registerDirectiveTranspiler("show", function ($expression) {
            $expression = empty($expression) ? "()" : $expression;

            return '<?php echo $__opulenceFortuneTranspiler->showPart' . $expression . '; ?>';
        });
        $transpiler->registerDirectiveTranspiler("while", function ($expression) {
            return '<?php while' . $expression . ': ?>';
        });
    }
}