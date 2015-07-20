<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the Fortune directive compile registrant
 */
namespace Opulence\Views\Compilers\SubCompilers\Fortune;

class FortuneDirectiveCompilerRegistrant
{
    /**
     * Registers the Fortune directive compilers
     *
     * @param FortuneCompiler $compiler The compiler to register to
     */
    public function registerDirectiveCompilers(FortuneCompiler &$compiler)
    {
        $compiler->registerDirectiveCompiler("else", function ()
        {
            return "<?php else: ?>";
        });
        $compiler->registerDirectiveCompiler("elseif", function ($expression)
        {
            return "<?php elseif($expression): ?>";
        });
        $compiler->registerDirectiveCompiler("elseifempty", function ()
        {
            return "<?php endforeach; if(array_pop(\$__opulenceForElseEmpty)): ?>";
        });
        $compiler->registerDirectiveCompiler("endforeach", function ()
        {
            return "<?php endforeach; ?>";
        });
        $compiler->registerDirectiveCompiler("endif", function ()
        {
            return "<?php endif; ?>";
        });
        $compiler->registerDirectiveCompiler("endfor", function ()
        {
            return "<?php endfor; ?>";
        });
        $compiler->registerDirectiveCompiler("endpart", function ()
        {
            return "<?php \$__opulenceFortuneCompiler->endPart(); ?>";
        });
        $compiler->registerDirectiveCompiler("endwhile", function ()
        {
            return "<?php endwhile; ?>";
        });
        $compiler->registerDirectiveCompiler("extends", function ($expression)
        {
            $code = "<?php \$__opulenceParentTemplate = \$__opulenceTemplateFactory->create($expression);";
            $code .= "\$__opulenceCompiler->compile(\$__opulenceParentTemplate, \$__opulenceParentTemplate->getContents()); ?>";
            $code = addcslashes($code, '"');

            return "<?php \$__opulenceFortuneCompiler->append(\"$code\"); ?>";

        });
        $compiler->registerDirectiveCompiler("for", function ($expression)
        {
            return "<?php for($expression): ?>";
        });
        $compiler->registerDirectiveCompiler("foreach", function ($expression)
        {
            return "<?php foreach($expression): ?>";
        });
        $compiler->registerDirectiveCompiler("forelse", function ($expression)
        {
            $code = "<?php if(!isset(\$__opulenceForElseEmpty): \$__opulenceForElseEmpty = []; endif;";
            $code .= "\$__opulenceForElseEmpty[] = true;";
            $code .= "foreach($expression):";
            $code .= "\$__opulenceForElseEmpty[count(\$__opulenceForElseEmpty) - 1] = true; ?>";

            return $code;
        });
        $compiler->registerDirectiveCompiler("if", function ($expression)
        {
            return "<?php if($expression): ?>";
        });
        $compiler->registerDirectiveCompiler("include", function ($expression)
        {
            $code = "<?php \$__opulenceIncludedTemplate = \$__opulenceTemplateFactory->create($expression);";
            $code .= "\$__opulenceCompiler->compile(\$__opulenceIncludedTemplate, \$__opulenceIncludedTemplate->getContents()); ?>";

            return $code;
        });
        $compiler->registerDirectiveCompiler("parent", function ()
        {
            // This placeholder will be overwritten later
            return "__opulenceParentPlaceholder";
        });
        $compiler->registerDirectiveCompiler("part", function ($expression)
        {
            return "<?php \$__opulenceFortuneCompiler->startPart($expression); ?>";
        });
        $compiler->registerDirectiveCompiler("show", function ($expression)
        {
            return "<?php \$__opulenceFortuneCompiler->showPart($expression); ?>";
        });
        $compiler->registerDirectiveCompiler("while", function ($expression)
        {
            return "<?php while($expression): ?>";
        });
    }
}