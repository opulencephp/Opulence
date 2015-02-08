<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the PHP sub-compiler
 */
namespace RDev\Views\Compilers\SubCompilers;
use RDev\Views;
use RDev\Views\Compilers;
use RDev\Views\Filters;

class PHPCompiler extends SubCompiler
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

        // Compile the functions
        $templateFunctions = $this->parentCompiler->getTemplateFunctions();
        $escapedCount = 1;
        $unescapedCount = 1;

        /**
         * This is done inside a loop in case any function returns another function
         * We keep evaluating the template until all the functions are evaluated
         */
        do
        {
            $escapedDelimiters = $template->getDelimiters(Views\ITemplate::DELIMITER_TYPE_ESCAPED_TAG);
            $unescapedDelimiters = $template->getDelimiters(Views\ITemplate::DELIMITER_TYPE_UNESCAPED_TAG);

            // Keep track of our output buffer level so we know how many to clean when we're done
            $startOBLevel = ob_get_level();
            ob_start();

            // We allow any valid php function name
            /** @link http://php.net/manual/en/functions.user-defined.php */
            $regex = "/%s\s*([a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*)\(\s*((?:(?!\)\s*%s).)*)\s*\)\s*%s/";
            $functionCallString = 'call_user_func_array($templateFunctions["\1"], [\2])';
            // Replace function calls in escaped tags
            $content = preg_replace(
                sprintf(
                    $regex,
                    preg_quote($escapedDelimiters[0], "/"),
                    preg_quote($escapedDelimiters[1], "/"),
                    preg_quote($escapedDelimiters[1], "/")),
                '<?php echo $this->xssFilter->run(' . $functionCallString . '); ?>',
                $content,
                -1,
                $escapedCount
            );
            // Replace function calls in unescaped tags
            $content = preg_replace(
                sprintf(
                    $regex,
                    preg_quote($unescapedDelimiters[0], "/"),
                    preg_quote($unescapedDelimiters[1], "/"),
                    preg_quote($unescapedDelimiters[1], "/")),
                "<?php echo $functionCallString; ?>",
                $content,
                -1,
                $unescapedCount
            );
            // Replace any variables inside escaped tags
            $variableTagRegex = '/(%s\s*)\$(%s)(\s*%s)/';
            $content = preg_replace(
                sprintf(
                    $variableTagRegex,
                    preg_quote($escapedDelimiters[0], "/"),
                    "[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*",
                    preg_quote($escapedDelimiters[1], "/")
                ),
                '$1"<?php echo addcslashes($$2, \'"\'); ?>"$3',
                $content
            );
            // Replace any variables inside unescaped tags
            $content = preg_replace(
                sprintf(
                    $variableTagRegex,
                    preg_quote($unescapedDelimiters[0], "/"),
                    "[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*",
                    preg_quote($unescapedDelimiters[1], "/")
                ),
                '$1"<?php echo addcslashes($$2, \'"\'); ?>"$3',
                $content
            );

            // Notice the little hack inside eval() to compile inline PHP
            if(eval("?>" . $content) === false)
            {
                // Prevent an invalid template from displaying
                while(ob_get_level() > $startOBLevel)
                {
                    ob_end_clean();
                }

                throw new Compilers\ViewCompilerException("Invalid PHP inside template");
            }

            $content = ob_get_clean();
        }
        while($escapedCount > 0 || $unescapedCount > 0);

        return $content;
    }
}