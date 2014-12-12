<?php
/**
 * Copyright (C) 2014 David Young
 * 
 * Defines the PHP sub-compiler
 */
namespace RDev\Views\Compilers\SubCompilers;
use RDev\Views;
use RDev\Views\Compilers;
use RDev\Views\Filters;

class PHP extends SubCompiler
{
    /** @var Filters\IFilter The cross-site scripting filter */
    private $xssFilter = null;

    /**
     * {@inheritdoc}
     * @param Filters\IFilter $xssFilter The cross-site scripting filter
     */
    public function __construct(Compilers\ICompiler $parentCompiler, Filters\IFilter $xssFilter)
    {
        parent::__construct($parentCompiler);

        $this->xssFilter = $xssFilter;
    }

    /**
     * {@inheritdoc}
     */
    public function compile(Views\ITemplate $template, $content)
    {
        // Create local variables for use in eval()
        extract($template->getVars());

        // Keep track of our output buffer level so we know how many to clean when we're done
        $startOBLevel = ob_get_level();
        ob_start();

        // Compile the functions
        $templateFunctions = $this->parentCompiler->getTemplateFunctions();

        foreach($templateFunctions as $functionName => $callback)
        {
            $regex = "/%s\s*%s\(\s*((?:(?!\)\s*%s).)*)\s*\)\s*%s/";
            $functionCallString = 'call_user_func_array($templateFunctions["' . $functionName . '"], [\1])';
            // Replace function calls in escaped tags
            $content = preg_replace(
                sprintf(
                    $regex,
                    preg_quote($template->getEscapedOpenTag(), "/"),
                    preg_quote($functionName, "/"),
                    preg_quote($template->getEscapedCloseTag(), "/"),
                    preg_quote($template->getEscapedCloseTag(), "/")),
                '<?php echo $this->xssFilter->run(' . $functionCallString . '); ?>',
                $content
            );
            // Replace function calls in unescaped tags
            $content = preg_replace(
                sprintf(
                    $regex,
                    preg_quote($template->getUnescapedOpenTag(), "/"),
                    preg_quote($functionName, "/"),
                    preg_quote($template->getUnescapedCloseTag(), "/"),
                    preg_quote($template->getUnescapedCloseTag(), "/")),
                "<?php echo $functionCallString; ?>",
                $content
            );
        }

        // Notice the little hack inside eval() to compile inline PHP
        if(eval("?>" . $content) === false)
        {
            // Prevent an invalid template from displaying
            while(ob_get_level() > $startOBLevel)
            {
                ob_end_clean();
            }

            throw new \RuntimeException("Invalid PHP inside template");
        }

        return ob_get_clean();
    }
}