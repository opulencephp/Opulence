<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the Fortune compiler
 */
namespace Opulence\Views\Compilers\Fortune;
use InvalidArgumentException;
use Opulence\Views\Compilers\ICompiler;
use Opulence\Views\Compilers\Fortune\Lexers\ILexer;
use Opulence\Views\Compilers\Fortune\Parsers\AbstractSyntaxTree;
use Opulence\Views\Compilers\Fortune\Parsers\IParser;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\DirectiveNode;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\ExpressionNode;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\Node;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\SanitizedTagNode;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\UnsanitizedTagNode;
use Opulence\Views\Filters\XSSFilter;
use Opulence\Views\IFortuneView;
use Opulence\Views\IView;
use RuntimeException;

class FortuneCompiler implements ICompiler
{
    /** @var ILexer The view lexer */
    protected $lexer = null;
    /** @var IParser The view parser */
    protected $parser = null;
    /** @var XSSFilter The XSS filter to use to sanitize text */
    protected $xssFilter = null;
    /** @var callable[] The mapping of directive names to their compilers */
    protected $directiveCompilers = [];
    /** @var callable[] The mapping of view function names to their definitions */
    protected $viewFunctions = [];
    /** @var bool Whether or not we're in a parent part */
    protected $inParentPart = false;
    /** @var array The mapping of part names to their contents */
    protected $parts = [];
    /** @var array The stack of parts */
    protected $partStack = [];
    /** @var array Any PHP appended to the end of the view */
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
        // Register built-in view functions
        (new ViewFunctionRegistrant())->registerViewFunctions($this);
        // Register built-in directives' compilers
        (new DirectiveCompilerRegistrant)->registerDirectiveCompilers($this);
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
     * Calls a view function
     * Pass in any arguments as the 2nd, 3rd, 4th, etc parameters
     *
     * @param string $functionName The name of the function to call
     * @return mixed The output of the view function
     * @throws InvalidArgumentException Thrown if the function name is invalid
     */
    public function callViewFunction($functionName)
    {
        if(!isset($this->viewFunctions[$functionName]))
        {
            throw new InvalidArgumentException("View function \"$functionName\" does not exist");
        }

        $args = func_get_args();
        array_shift($args);

        return call_user_func_array($this->viewFunctions[$functionName], $args);
    }

    /**
     * @inheritdoc
     * @param IFortuneView $view
     */
    public function compile(IView $view, $contents = null)
    {
        $tokens = $this->lexer->lex($view, $contents);
        $ast = $this->parser->parse($tokens);
        $compiledContent = $this->compileNodes($ast);

        if(count($this->appendedText) > 0)
        {
            // Format the content nicely
            $compiledContent = ltrim($compiledContent, PHP_EOL) . PHP_EOL . implode(PHP_EOL, $this->appendedText);
        }

        return $compiledContent;
    }

    /**
     * Ends a view part
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
     * Registers a function that appears in a view
     * Useful for defining functions for consistent formatting in a view
     *
     * @param string $functionName The name of the function as it'll appear in the view
     * @param callable $function The function that returns the replacement string for the function in a view
     *      It must accept one parameter (the view's contents) and return a printable value
     */
    public function registerViewFunction($functionName, callable $function)
    {
        $this->viewFunctions[$functionName] = $function;
    }

    /**
     * Sanitizes a value
     *
     * @param mixed $value The value to sanitize
     * @return string The sanitized value
     */
    public function sanitize($value)
    {
        return $this->xssFilter->run($value);
    }

    /**
     * Shows a view part
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
     * Starts a view part
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
        $expression = count($children) == 2 ? $this->replaceFunctionCalls($children[1]->getValue()) : "";

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
     * @param AbstractSyntaxTree $ast The abstract syntax tree to compile
     * @return string The view with compiled nodes
     * @throws RuntimeException Thrown if the nodes could not be compiled
     */
    protected function compileNodes(AbstractSyntaxTree $ast)
    {
        $compiledView = "";
        $rootNode = $ast->getRootNode();

        foreach($rootNode->getChildren() as $childNode)
        {
            switch(get_class($childNode))
            {
                case DirectiveNode::class:
                    $compiledView .= $this->compileDirectiveNode($childNode);

                    break;
                case SanitizedTagNode::class:
                    $compiledView .= $this->compileSanitizedTagNode($childNode);

                    break;
                case UnsanitizedTagNode::class:
                    $compiledView .= $this->compileUnsanitizedTagNode($childNode);

                    break;
                case ExpressionNode::class:
                    $compiledView .= $this->compileExpressionNode($childNode);

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

        return $compiledView;
    }

    /**
     * Compiles a sanitized tag node
     *
     * @param Node $node The node to compile
     * @return string The compiled node
     */
    protected function compileSanitizedTagNode(Node $node)
    {
        return "<?php echo \$__opulenceFortuneCompiler->sanitize({$this->replaceFunctionCalls($node->getValue())}); ?>";
    }

    /**
     * Compiles an unsanitized tag node
     *
     * @param Node $node The node to compile
     * @return string The compiled node
     */
    protected function compileUnsanitizedTagNode(Node $node)
    {
        return "<?php echo {$this->replaceFunctionCalls($node->getValue())}; ?>";
    }

    /**
     * Replaces any view functions calls with calls to valid PHP functions
     *
     * @param string $content The content to replace
     * @return string The replaced content
     */
    protected function replaceFunctionCalls($content)
    {
        $callback = function (array $matches)
        {
            if(function_exists($matches[1]))
            {
                // This was a PHP function
                return $matches[1] . '(' . $this->replaceFunctionCalls($matches[3]) . ')';
            }
            else
            {
                // This was a view function
                return '$__opulenceFortuneCompiler->callViewFunction(' .
                '"' . $matches[1] . '", ' . $this->replaceFunctionCalls($matches[3]) .
                ')';
            }
        };

        return preg_replace_callback(
            "/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)(\((((?>[^()]+)|(?2))*)\))/",
            $callback,
            $content
        );
    }
}