<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the tag sub-compiler
 */
namespace RDev\Views\Compilers\SubCompilers;
use RDev\Views;
use RDev\Views\Compilers;
use RDev\Views\Filters;

class TagCompiler extends SubCompiler
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
        // Store some data about our callbacks
        // Store the template functions to a local variable so they can be used when we eval() the generated PHP code
        $templateFunctions = $this->parentCompiler->getTemplateFunctions();
        $tagData = $this->getTagData($template);

        // Keep track of our output buffer level so we know how many to clean when we're done
        $startOBLevel = ob_get_level();
        ob_start();

        try
        {
            $replacementCount = 1;

            // Compile tag contents
            foreach($tagData as $tagDataByType)
            {
                // Handle tags whose values are tags
                do
                {
                    $content = preg_replace_callback(
                        sprintf(
                            "/(?<!%s)%s\s*(.*)\s*%s/sU",
                            preg_quote("\\", "/"),
                            preg_quote($tagDataByType["delimiters"][0], "/"),
                            preg_quote($tagDataByType["delimiters"][1], "/")
                        ),
                        $tagDataByType["callback"],
                        $content,
                        -1,
                        $replacementCount
                    );
                }while($replacementCount > 0);
            }

            // Reset the count
            $replacementCount = 1;

            // Strip escape characters from escaped tags
            foreach($tagData as $tagDataByType)
            {
                // Handle tags whose values are tags
                do
                {
                    $content = preg_replace(
                        sprintf(
                            "/%s(%s\s*.*\s*%s)/sU",
                            preg_quote("\\", "/"),
                            preg_quote($tagDataByType["delimiters"][0], "/"),
                            preg_quote($tagDataByType["delimiters"][1], "/")
                        ),
                        "$1",
                        $content,
                        -1,
                        $replacementCount
                    );
                }while($replacementCount > 0);
            }

            // Create local variables for use in eval()
            extract($template->getVars());

            if(eval("?>" . $content) === false)
            {
                throw new Compilers\ViewCompilerException("Invalid PHP inside template");
            }
        }
        catch(Compilers\ViewCompilerException $ex)
        {
            // Prevent an invalid template from displaying
            while(ob_get_level() > $startOBLevel)
            {
                ob_end_clean();
            }

            throw $ex;
        }

        return ob_get_clean();
    }

    /**
     * Gets the PHP code from a tag
     *
     * @param string $tagContents The tag contents
     * @return string The PHP code
     * @throws Compilers\ViewCompilerException Thrown if there was an error generating the PHP
     */
    private function generatePHP($tagContents)
    {
        if(empty($tagContents))
        {
            return "";
        }

        $phpTokens = token_get_all("<?php $tagContents ?>");
        $rDevTokens = [];
        $templateFunctionNames = array_keys($this->parentCompiler->getTemplateFunctions());

        foreach($phpTokens as $index => $token)
        {
            if(is_string($token))
            {
                // Convert the simple token to an array for uniformity
                $rDevTokens[] = [T_STRING, $token, 0];

                continue;
            }

            switch($token[0])
            {
                case T_STRING:
                    // If this is a function
                    if($this->peek($phpTokens, $index) == "(")
                    {
                        // If this is a template function
                        if(in_array($token[1], $templateFunctionNames))
                        {
                            // Add $templateFunctions
                            $rDevTokens[] = [T_VARIABLE, "\$templateFunctions", $token[2]];
                            // Add [
                            $rDevTokens[] = [T_STRING, "[", $token[2]];
                            // Add function name
                            $rDevTokens[] = [T_STRING, '"' . $token[1] . '"', $token[2]];
                            // Add ]
                            $rDevTokens[] = [T_STRING, "]", $token[2]];
                        }
                        else
                        {
                            $previousToken = $this->lookBehind($phpTokens, $index);

                            if(
                                is_array($previousToken) && $previousToken[0] !== T_OBJECT_OPERATOR &&
                                $previousToken[0] !== T_DOUBLE_COLON && !function_exists($token[1]))
                            {
                                throw new Compilers\ViewCompilerException(
                                    "Template function \"{$token[1]}\" does not exist"
                                );
                            }

                            $rDevTokens[] = $token;
                        }
                    }
                    else
                    {
                        $rDevTokens[] = $token;
                    }

                    break;
                default:
                    $rDevTokens[] = $token;

                    break;
            }
        }

        // Remove php open/close tags
        array_shift($rDevTokens);
        array_pop($rDevTokens);

        // Rejoin token values
        return implode(" ", array_column($rDevTokens, 1));
    }

    /**
     * Gets an array of data with details about how to compile each type of tag
     *
     * @param Views\ITemplate $template The template that is being compiled
     * @return array The array of tag data
     */
    private function getTagData(Views\ITemplate $template)
    {
        $escapedDelimiters = $template->getDelimiters(Views\ITemplate::DELIMITER_TYPE_ESCAPED_TAG);
        $unescapedDelimiters = $template->getDelimiters(Views\ITemplate::DELIMITER_TYPE_UNESCAPED_TAG);
        $escapedTagData = [
            "delimiters" => [$escapedDelimiters[0], $escapedDelimiters[1]],
            "callback" => function(array $matches) use ($template)
            {
                if(($tagValue = $this->getTagValue($template, $matches[1])) !== null)
                {
                    return $this->xssFilter->run($tagValue);
                }

                if(($code = $this->generatePHP($matches[1])) == "")
                {
                    return "";
                }

                // In the case of code being a string literal eg "foo", we don't want to sanitize the quotes
                // So, we set a temporary variable to the string literal, then sanitize the variable
                return '<?php $__rdevCompilerTmp = ' . $code . '; echo $this->xssFilter->run($__rdevCompilerTmp); ?>';
            }
        ];
        $unescapedTagData = [
            "delimiters" => [$unescapedDelimiters[0], $unescapedDelimiters[1]],
            "callback" => function(array $matches) use ($template)
            {
                if(($tagValue = $this->getTagValue($template, $matches[1])) !== null)
                {
                    return $tagValue;
                }

                if(($code = $this->generatePHP($matches[1])) == "")
                {
                    return "";
                }

                return "<?php echo $code; ?>";
            }
        ];

        // In the case that one open tag is a substring of another (eg "{{" and "{{{"), handle the longer one first
        // If they're the same length, they cannot be substrings of one another unless they're equal
        if(strlen($escapedDelimiters[0]) > strlen($unescapedDelimiters[0]))
        {
            $tagData[] = $escapedTagData;
            $tagData[] = $unescapedTagData;
        }
        else
        {
            $tagData[] = $unescapedTagData;
            $tagData[] = $escapedTagData;
        }

        return $tagData;
    }

    /**
     * Gets the value of a tag, if there is one
     *
     * @param Views\ITemplate $template The template being compiled
     * @param string $tagContents The contents of the tag
     * @return mixed|null|string The tag value if there was one, otherwise null
     */
    private function getTagValue(Views\ITemplate $template, $tagContents)
    {
        $matches = [];

        // Check if the contents are simply a tag name
        if(
            preg_match(
                sprintf(
                // Account for multiple lines before and after tag name
                    "/^[%s\s]*([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)[%s\s]*$/",
                    preg_quote(PHP_EOL, "/"),
                    preg_quote(PHP_EOL, "/")
                ),
                $tagContents, $matches
            ) === 1
        )
        {
            $value = $template->getTag($matches[1]);

            // There was no tag with this name
            if($value === null)
            {
                return "";
            }

            return $value;
        }

        // This was not a tag name
        return null;
    }

    /**
     * Looks behind at the previous token
     *
     * @param array $tokens The list of all tokens
     * @param int $currIndex The index of the current token
     * @return null|array|string The previous token if there was one, otherwise null
     */
    private function lookBehind(array $tokens, $currIndex)
    {
        if($currIndex == 0)
        {
            return null;
        }

        return $tokens[$currIndex - 1];
    }

    /**
     * Peeks at the next token
     *
     * @param array $tokens The list of all tokens
     * @param int $currIndex The index of the current token
     * @return null|array|string The next token if there was one, otherwise null
     */
    private function peek(array $tokens, $currIndex)
    {
        if(count($tokens) == $currIndex + 1)
        {
            return null;
        }

        return $tokens[$currIndex + 1];
    }
}