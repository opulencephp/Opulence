<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the Fortune directive transpiler registrant
 */
namespace Opulence\Views\Compilers\Fortune;

class DirectiveTranspilerRegistrant
{
    /**
     * Registers the Fortune directive transpilers
     *
     * @param ITranspiler $transpiler The transpiler to register to
     */
    public function registerDirectiveTranspilers(ITranspiler &$transpiler)
    {
        $transpiler->registerDirectiveTranspiler("else", function ()
        {
            return "<?php else: ?>";
        });
        $transpiler->registerDirectiveTranspiler("elseif", function ($expression)
        {
            return "<?php elseif($expression): ?>";
        });
        $transpiler->registerDirectiveTranspiler("elseifempty", function ()
        {
            return "<?php endforeach; if(array_pop(\$__opulenceForElseEmpty)): ?>";
        });
        $transpiler->registerDirectiveTranspiler("endforeach", function ()
        {
            return "<?php endforeach; ?>";
        });
        $transpiler->registerDirectiveTranspiler("endif", function ()
        {
            return "<?php endif; ?>";
        });
        $transpiler->registerDirectiveTranspiler("endfor", function ()
        {
            return "<?php endfor; ?>";
        });
        $transpiler->registerDirectiveTranspiler("endpart", function ()
        {
            return "<?php \$__opulenceFortuneTranspiler->endPart(); ?>";
        });
        $transpiler->registerDirectiveTranspiler("endwhile", function ()
        {
            return "<?php endwhile; ?>";
        });
        $transpiler->registerDirectiveTranspiler("extends", function ($expression)
        {
            $code = "<?php \$__opulenceParentView = \$__opulenceViewFactory->create($expression);";
            $code .= "\$__opulenceViewCompiler->compile(\$__opulenceParentView, \$__opulenceParentView->getContents()); ?>";
            $code = addcslashes($code, '"');

            return "<?php \$__opulenceFortuneTranspiler->append(\"$code\"); ?>";

        });
        $transpiler->registerDirectiveTranspiler("for", function ($expression)
        {
            return "<?php for($expression): ?>";
        });
        $transpiler->registerDirectiveTranspiler("foreach", function ($expression)
        {
            return "<?php foreach($expression): ?>";
        });
        $transpiler->registerDirectiveTranspiler("forelse", function ($expression)
        {
            $code = "<?php if(!isset(\$__opulenceForElseEmpty)): \$__opulenceForElseEmpty = []; endif;";
            $code .= "\$__opulenceForElseEmpty[] = true;";
            $code .= "foreach($expression):";
            $code .= "\$__opulenceForElseEmpty[count(\$__opulenceForElseEmpty) - 1] = false; ?>";

            return $code;
        });
        $transpiler->registerDirectiveTranspiler("if", function ($expression)
        {
            return "<?php if($expression): ?>";
        });
        $transpiler->registerDirectiveTranspiler("include", function ($expression)
        {
            $code = "<?php \$__opulenceIncludedView = \$__opulenceViewFactory->create($expression);";
            $code .= "\$__opulenceViewCompiler->compile(\$__opulenceIncludedView, \$__opulenceIncludedView->getContents()); ?>";

            return $code;
        });
        $transpiler->registerDirectiveTranspiler("parent", function ()
        {
            // This placeholder will be overwritten later
            return "__opulenceParentPlaceholder";
        });
        $transpiler->registerDirectiveTranspiler("part", function ($expression)
        {
            return "<?php \$__opulenceFortuneTranspiler->startPart($expression); ?>";
        });
        $transpiler->registerDirectiveTranspiler("show", function ($expression)
        {
            return "<?php echo \$__opulenceFortuneTranspiler->showPart($expression); ?>";
        });
        $transpiler->registerDirectiveTranspiler("while", function ($expression)
        {
            return "<?php while($expression): ?>";
        });
    }
}