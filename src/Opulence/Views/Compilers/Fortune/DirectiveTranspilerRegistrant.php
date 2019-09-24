<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Views\Compilers\Fortune;

/**
 * Defines the Fortune directive transpiler registrant
 */
final class DirectiveTranspilerRegistrant
{
    /**
     * Registers the Fortune directive transpilers
     *
     * @param ITranspiler $transpiler The transpiler to register to
     */
    public function registerDirectiveTranspilers(ITranspiler $transpiler): void
    {
        $transpiler->registerDirectiveTranspiler('else', fn () => '<?php else: ?>');
        $transpiler->registerDirectiveTranspiler('elseif', fn ($expression) => '<?php elseif' . $expression . ': ?>');
        $transpiler->registerDirectiveTranspiler('forelse', fn () => '<?php endforeach; if(array_pop($__opulenceForElseEmpty)): ?>');
        $transpiler->registerDirectiveTranspiler('endforeach', fn () => '<?php endforeach; ?>');
        $transpiler->registerDirectiveTranspiler('endif', fn () => '<?php endif; ?>');
        $transpiler->registerDirectiveTranspiler('endfor', fn () => '<?php endfor; ?>');
        $transpiler->registerDirectiveTranspiler('endpart', fn () => '<?php $__opulenceFortuneTranspiler->endPart(); ?>');
        $transpiler->registerDirectiveTranspiler('endwhile', fn () => '<?php endwhile; ?>');
        $transpiler->registerDirectiveTranspiler('extends', function ($expression) use ($transpiler) {
            // Create the parent
            $code = '$__opulenceViewParent = $__opulenceViewFactory->createView' . $expression . ';';
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

            return '';
        });
        $transpiler->registerDirectiveTranspiler('for', fn ($expression) => '<?php for' . $expression . ': ?>');
        $transpiler->registerDirectiveTranspiler('foreach', fn ($expression) => '<?php foreach' . $expression . ': ?>');
        $transpiler->registerDirectiveTranspiler('forif', function ($expression) {
            $code = '<?php if(!isset($__opulenceForElseEmpty)): $__opulenceForElseEmpty = []; endif;';
            $code .= '$__opulenceForElseEmpty[] = true;';
            $code .= 'foreach' . $expression . ':';
            $code .= '$__opulenceForElseEmpty[count($__opulenceForElseEmpty) - 1] = false; ?>';

            return $code;
        });
        $transpiler->registerDirectiveTranspiler('if', fn ($expression) => '<?php if' . $expression . ': ?>');
        $transpiler->registerDirectiveTranspiler('include', function ($expression) {
            // Check if a list of variables were passed in as a second parameter
            if (
                preg_match(
                    "/^\(((('|\")(.*)\\3)|\\$[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*),\s*(.+)\)$/U",
                    $expression,
                    $matches
                ) === 1
            ) {
                $sharedVars = trim($matches[5]);
                $factoryCreateCall = 'createView(' . $matches[1] . ')';
            } else {
                $sharedVars = '[]';
                $factoryCreateCall = 'createView' . $expression;
            }

            // Create an isolate scope for the included view
            $code = 'call_user_func(function() use ($__opulenceViewFactory, $__opulenceFortuneTranspiler){';
            $code .= '$__opulenceIncludedView = $__opulenceViewFactory->' . $factoryCreateCall . ';';
            $code .= 'extract($__opulenceIncludedView->getVars());';
            // Extract any shared vars, which will override any identically-named view vars
            $code .= 'if(count(func_get_arg(0)) > 0){extract(func_get_arg(0));}';
            $code .= 'eval("?>" . $__opulenceFortuneTranspiler->transpile($__opulenceIncludedView));';
            $code .= '}, ' . $sharedVars . ');';

            return "<?php $code ?>";
        });
        $transpiler->registerDirectiveTranspiler('parent', function () {
            // This placeholder will be overwritten later
            return '__opulenceParentPlaceholder';
        });
        $transpiler->registerDirectiveTranspiler('part', fn ($expression) =>'<?php $__opulenceFortuneTranspiler->startPart' . $expression . '; ?>');
        $transpiler->registerDirectiveTranspiler('show', function ($expression) {
            $expression = empty($expression) ? '()' : $expression;

            return '<?php echo $__opulenceFortuneTranspiler->showPart' . $expression . '; ?>';
        });
        $transpiler->registerDirectiveTranspiler('while', fn ($expression) => '<?php while' . $expression . ': ?>');
    }
}
