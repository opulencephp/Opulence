<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the Fortune compiler
 */
namespace Opulence\Views\Compilers\SubCompilers\Fortune;
use InvalidArgumentException;
use Opulence\Views\Compilers\SubCompilers\ISubCompiler;
use Opulence\Views\Filters\XSSFilter;
use Opulence\Views\Compilers\Lexers\ILexer;
use Opulence\Views\Compilers\Parsers\AbstractSyntaxTree;
use Opulence\Views\Compilers\Parsers\IParser;
use Opulence\Views\Compilers\Parsers\Nodes\DirectiveNode;
use Opulence\Views\Compilers\Parsers\Nodes\ExpressionNode;
use Opulence\Views\Compilers\Parsers\Nodes\Node;
use Opulence\Views\Compilers\Parsers\Nodes\SanitizedTagNode;
use Opulence\Views\Compilers\Parsers\Nodes\UnsanitizedTagNode;
use Opulence\Views\ITemplate;
use RuntimeException;

class FortuneCompiler implements ISubCompiler
{
    /** @var ILexer The view lexer */
    protected $lexer = null;
    /** @var IParser The view parser */
    protected $parser = null;
    /** @var XSSFilter The XSS filter to use to sanitize text */
    protected $xssFilter = null;
    /** @var callable[] The mapping of directive names to their compilers */
    protected $directiveCompilers = [];
    /** @var callable[] The mapping of template function names to their definitions */
    protected $templateFunctions = [];
    /** @var bool Whether or not we're in a parent part */
    protected $inParentPart = false;
    /** @var array The mapping of part names to their contents */
    protected $parts = [];
    /** @var array The stack of parts */
    protected $partStack = [];
    /** @var array Any PHP appended to the end of the template */
    protected $appendedText = [];

    /**
     * @param ILexer $lexer The view lexer
     * @param IParser $parser The view parser
     * @param XSSFilter $xssFilter The XSS filter
     */
    public function __construct(ILexer $lexer, IParser $parser, XSSFilter $xssFilter)
    {
        $this->lexer = $lexer;
        $this->parser = $parser;
        $this->xssFilter = $xssFilter;
        // Register built-in template functions
        (new FortuneTemplateFunctionRegistrant())->registerTemplateFunctions($this);
        // Register built-in directives' compilers
        (new FortuneDirectiveCompilerRegistrant)->registerDirectiveCompilers($this);
    }

    /**
     * Appends text to the end of the compiled contents
     *
     * @param string $text The text to append
     */
    public function append($text)
    {
        $this->appendedText[] = $text;
    }

    /**
     * Calls a template function
     * Pass in any arguments as the 2nd, 3rd, 4th, etc parameters
     *
     * @param string $functionName The name of the function to call
     * @return mixed The output of the template function
     * @throws InvalidArgumentException Thrown if the function name is invalid
     */
    public function callTemplateFunction($functionName)
    {
        if(!isset($this->templateFunctions[$functionName]))
        {
            throw new InvalidArgumentException("Template function \"$functionName\" does not exist");
        }

        $args = func_get_args();
        array_shift($args);

        return call_user_func_array($this->templateFunctions[$functionName], $args);
    }

    /**
     * Compiles a template into raw PHP
     *
     * @param ITemplate $template The template to compile
     * @param string $content The content to compile
     * @return string The compiled PHP
     * @throws RuntimeException Thrown if there was an error compiling the template
     */
    public function compile(ITemplate $template, $content)
    {
        $tokens = $this->lexer->lex($template, $content);
        $ast = $this->parser->parse($tokens);
        $compiledContent = $this->compileNodes($template, $ast);

        if(count($this->appendedText) > 0)
        {
            // Format the content nicely
            $compiledContent = ltrim($compiledContent, PHP_EOL) . PHP_EOL . implode(PHP_EOL, $this->appendedText);
        }

        return $compiledContent;
    }

    /**
     * Ends a template part
     */
    public function endPart()
    {
        $partName = array_pop($this->partStack);
        $content = ob_get_clean();

        if($this->inParentPart)
        {
            // Now that we know the value of the parent, replace the placeholder
            $this->parts[$partName] = str_replace("__opulenceParentPlaceholder", $content, $this->parts[$partName]);
            $this->inParentPart = false;
        }
        else
        {
            $this->parts[$partName] = $content;
        }
    }

    /**
     * Registers a directive compiler
     *
     * @param string $name The name of the directive whose compiler we're registering
     * @param callable $compiler The compiler, which accepts an optional expression from the directive
     */
    public function registerDirectiveCompiler($name, callable $compiler)
    {
        $this->directiveCompilers[$name] = $compiler;
    }

    /**
     * {@inheritdoc}
     */
    public function registerTemplateFunction($functionName, callable $function)
    {
        $this->templateFunctions[$functionName] = $function;
    }

    /**
     * Sanitized a value
     *
     * @param mixed $value The value to sanitize
     * @return string The sanitized value
     */
    public function sanitize($value)
    {
        return $this->xssFilter->run($value);
    }

    /**
     * Shows a template part
     *
     * @param string $name The name of the part to show
     * @return string The content of the part
     */
    public function showPart($name)
    {
        if(!isset($this->parts[$name]))
        {
            return "";
        }

        return $this->parts[$name];
    }

    /**
     * Starts a template part
     *
     * @param string $name The name of the part to start
     */
    public function startPart($name)
    {
        // If this part already exists, we consider it to be a parent part
        $this->inParentPart = isset($this->parts[$name]);
        $this->partStack[] = $name;

        ob_start();
    }

    /**
     * Compiles a directive node
     *
     * @param Node $node The node to compile
     * @return string The compiled node
     * @throws RuntimeException Thrown if the directive could not be compiled
     */
    protected function compileDirectiveNode(Node $node)
    {
        $children = $node->getChildren();

        if(count($children) == 0)
        {
            return "";
        }

        $directiveName = $children[0]->getValue();
        $expression = count($children) == 2 ? $this->replaceTemplatefunctionCalls($children[1]->getValue()) : "";

        if(!isset($this->directiveCompilers[$directiveName]))
        {
            throw new RuntimeException(
                sprintf(
                    'No compiler registered for directive "%s"',
                    $directiveName
                )
            );
        }

        return call_user_func($this->directiveCompilers[$directiveName], $expression);
    }

    /**
     * Compiles an expression node
     *
     * @param Node $node The node to compile
     * @return string The compiled node
     */
    protected function compileExpressionNode(Node $node)
    {
        return $node->getValue();
    }

    /**
     * Compiles all nodes in an abstract syntax tree
     *
     * @param ITemplate $template The template that's being compiled
     * @param AbstractSyntaxTree $ast The abstract syntax tree to compile
     * @return string The template with compiled nodes
     * @throws RuntimeException Thrown if the nodes could not be compiled
     */
    protected function compileNodes(ITemplate $template, AbstractSyntaxTree $ast)
    {
        $compiledTemplate = "";
        $rootNode = $ast->getRootNode();

        foreach($rootNode->getChildren() as $childNode)
        {
            switch(get_class($childNode))
            {
                case DirectiveNode::class:
                    $compiledTemplate .= $this->compileDirectiveNode($childNode);

                    break;
                case SanitizedTagNode::class:
                    $compiledTemplate .= $this->compileSanitizedTagNode($childNode, $template);

                    break;
                case UnsanitizedTagNode::class:
                    $compiledTemplate .= $this->compileUnsanitizedTagNode($childNode, $template);

                    break;
                case ExpressionNode::class:
                    $compiledTemplate .= $this->compileExpressionNode($childNode);

                    break;
                default:
                    throw new RuntimeException(
                        sprintf(
                            "Unknown node class %s",
                            get_class($childNode)
                        )
                    );
            }
        }

        return $compiledTemplate;
    }

    /**
     * Compiles a sanitized tag node
     *
     * @param Node $node The node to compile
     * @param ITemplate $template The template being compiled
     * @return string The compiled node
     */
    protected function compileSanitizedTagNode(Node $node, ITemplate $template)
    {
        $compiledExpression = $this->replaceTemplateFunctionCalls($node->getValue());

        return "<?php echo \$__opulenceFortuneCompiler->sanitize({$this->compileTagValue($template, $compiledExpression)}); ?>";
    }

    /**
     * Compiles an unsanitized tag node
     *
     * @param Node $node The node to compile
     * @param ITemplate $template The template being compiled
     * @return string The compiled node
     */
    protected function compileUnsanitizedTagNode(Node $node, ITemplate $template)
    {
        $compiledExpression = $this->replaceTemplateFunctionCalls($node->getValue());

        return "<?php echo {$this->compileTagValue($template, $compiledExpression)}; ?>";
    }

    /**
     * Replaces any template functions calls with calls to valid PHP functions
     *
     * @param string $content The content to replace
     * @return string The replaced content
     */
    protected function replaceTemplateFunctionCalls($content)
    {
        return preg_replace(
            "/View::([^\(]+)\(/",
            '$__opulenceFortuneCompiler->callTemplateFunction("$1", ',
            $content
        );
    }

    /**
     * Gets the value of a tag
     * If the input is the name of a tag, that tag's contents are returned
     * If it's an expression, the expression is returned
     *
     * @param ITemplate $template
     * @param mixed $value The value
     * @return string The compiled tag value
     */
    private function compileTagValue(ITemplate $template, $value)
    {
        if(
            preg_match(
                sprintf(
                // Account for multiple lines before and after tag name
                    "/^[%s\s]*([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)[%s\s]*$/",
                    preg_quote(PHP_EOL, "/"),
                    preg_quote(PHP_EOL, "/")
                ),
                $value, $matches
            ) === 1
        )
        {
            $value = $template->getTag($matches[1]);

            // There was no tag with this name
            if($value === null)
            {
                return "";
            }

            return '"' . addslashes($value) . '"';
        }

        return $value;
    }
}