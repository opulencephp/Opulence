<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Views\Compilers\Fortune;

use InvalidArgumentException;
use Opulence\Views\Caching\ICache;
use Opulence\Views\Compilers\Fortune\Lexers\ILexer;
use Opulence\Views\Compilers\Fortune\Parsers\AbstractSyntaxTree;
use Opulence\Views\Compilers\Fortune\Parsers\IParser;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\CommentNode;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\DirectiveNode;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\ExpressionNode;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\Node;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\SanitizedTagNode;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\UnsanitizedTagNode;
use Opulence\Views\Filters\XssFilter;
use Opulence\Views\IView;
use RuntimeException;

/**
 * Defines the Fortune transpiler
 */
class Transpiler implements ITranspiler
{
    /** @var ILexer The view lexer */
    protected $lexer = null;
    /** @var IParser The view parser */
    protected $parser = null;
    /** @var ICache The transpiled view cache */
    protected $cache = null;
    /** @var XssFilter The XSS filter to use to sanitize text */
    protected $xssFilter = null;
    /** @var callable[] The mapping of directive names to their transpilers */
    protected $directiveTranspilers = [];
    /** @var callable[] The mapping of view function names to their definitions */
    protected $viewFunctions = [];
    /** @var bool Whether or not we're in a parent part */
    protected $inParentPart = false;
    /** @var array The mapping of part names to their contents */
    protected $parts = [];
    /** @var array The stack of parts */
    protected $partStack = [];
    /** @var array Any PHP prepended to the beginning of the view */
    protected $prependedText = [];
    /** @var array Any PHP appended to the end of the view */
    protected $appendedText = [];

    /**
     * @param ILexer $lexer The view lexer
     * @param IParser $parser The view parser
     * @param ICache $cache The view cache
     * @param XssFilter $xssFilter The XSS filter
     */
    public function __construct(ILexer $lexer, IParser $parser, ICache $cache, XssFilter $xssFilter)
    {
        $this->lexer = $lexer;
        $this->parser = $parser;
        $this->cache = $cache;
        $this->xssFilter = $xssFilter;
        // Register built-in view functions
        (new ViewFunctionRegistrant())->registerViewFunctions($this);
        // Register built-in directives' transpilers
        (new DirectiveTranspilerRegistrant)->registerDirectiveTranspilers($this);
    }

    /**
     * @inheritdoc
     */
    public function addParent(IView $parent, IView $child)
    {
        foreach ($parent->getVars() as $name => $value) {
            if (!$child->hasVar($name)) {
                $child->setVar($name, $value);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function append(string $text)
    {
        $this->appendedText[] = $text;
    }

    /**
     * @inheritdoc
     */
    public function callViewFunction(string $functionName, ...$args)
    {
        if (!isset($this->viewFunctions[$functionName])) {
            throw new InvalidArgumentException("View function \"$functionName\" does not exist");
        }

        return $this->viewFunctions[$functionName](...$args);
    }

    /**
     * @inheritdoc
     */
    public function endPart()
    {
        $partName = array_pop($this->partStack);
        $content = ob_get_clean();

        if ($this->inParentPart) {
            // Now that we know the value of the parent, replace the placeholder
            $this->parts[$partName] = str_replace('__opulenceParentPlaceholder', $content, $this->parts[$partName]);
            $this->inParentPart = false;
        } else {
            $this->parts[$partName] = $content;
        }
    }

    /**
     * @inheritdoc
     */
    public function prepend(string $text)
    {
        $this->prependedText[] = $text;
    }

    /**
     * @inheritdoc
     */
    public function registerDirectiveTranspiler(string $name, callable $transpiler)
    {
        $this->directiveTranspilers[$name] = $transpiler;
    }

    /**
     * @inheritdoc
     */
    public function registerViewFunction(string $functionName, callable $function)
    {
        $this->viewFunctions[$functionName] = $function;
    }

    /**
     * @inheritdoc
     */
    public function sanitize($value) : string
    {
        return $this->xssFilter->run($value);
    }

    /**
     * @inheritdoc
     */
    public function showPart(string $name = '') : string
    {
        if (empty($name)) {
            $name = end($this->partStack);
            reset($this->partStack);
            $this->endPart();
        }

        if (!isset($this->parts[$name])) {
            return '';
        }

        return $this->parts[$name];
    }

    /**
     * @inheritdoc
     */
    public function startPart(string $name)
    {
        // If this part already exists, we consider it to be a parent part
        $this->inParentPart = isset($this->parts[$name]);
        $this->partStack[] = $name;

        ob_start();
    }

    /**
     * @inheritdoc
     */
    public function transpile(IView $view) : string
    {
        $this->appendedText = [];
        $this->prependedText = [];

        if (($transpiledContent = $this->cache->get($view, false)) !== null) {
            return $transpiledContent;
        }

        $tokens = $this->lexer->lex($view);
        $ast = $this->parser->parse($tokens);
        $transpiledContent = $this->transpileNodes($ast);

        if (count($this->prependedText) > 0) {
            // Format the content nicely
            $transpiledContent = trim(implode(PHP_EOL, $this->prependedText), PHP_EOL) . PHP_EOL . $transpiledContent;
        }

        if (count($this->appendedText) > 0) {
            // Format the content nicely
            $transpiledContent = trim($transpiledContent, PHP_EOL) . PHP_EOL . implode(PHP_EOL, $this->appendedText);
        }

        $this->cache->set($view, $transpiledContent, false);

        return $transpiledContent;
    }

    /**
     * Transpiles a comment node
     *
     * @param Node $node The node to transpile
     * @return string The transpiled node
     */
    protected function transpileCommentNode(Node $node) : string
    {
        $code = '';

        foreach ($node->getChildren() as $childNode) {
            $code .= '<?php /* ' . $childNode->getValue() . ' */ ?>';
        }

        return $code;
    }

    /**
     * Transpiles a directive node
     *
     * @param Node $node The node to transpile
     * @return string The transpiled node
     * @throws RuntimeException Thrown if the directive could not be transpiled
     */
    protected function transpileDirectiveNode(Node $node) : string
    {
        $children = $node->getChildren();

        if (count($children) === 0) {
            return '';
        }

        $directiveName = $children[0]->getValue();
        $expression = count($children) === 2 ? $children[1]->getValue() : '';

        if (!isset($this->directiveTranspilers[$directiveName])) {
            throw new RuntimeException(
                sprintf(
                    'No transpiler registered for directive "%s"',
                    $directiveName
                )
            );
        }

        return $this->directiveTranspilers[$directiveName]($expression);
    }

    /**
     * Transpiles an expression node
     *
     * @param Node $node The node to transpile
     * @return string The transpiled node
     */
    protected function transpileExpressionNode(Node $node) : string
    {
        return $node->getValue();
    }

    /**
     * Transpiles all nodes in an abstract syntax tree
     *
     * @param AbstractSyntaxTree $ast The abstract syntax tree to transpile
     * @return string The view with transpiled nodes
     * @throws RuntimeException Thrown if the nodes could not be transpiled
     */
    protected function transpileNodes(AbstractSyntaxTree $ast) : string
    {
        $transpiledView = '';
        $rootNode = $ast->getRootNode();
        $previousNodeWasExpression = false;

        foreach ($rootNode->getChildren() as $childNode) {
            switch (get_class($childNode)) {
                case DirectiveNode::class:
                    $transpiledView .= $this->transpileDirectiveNode($childNode);
                    $previousNodeWasExpression = false;

                    break;
                case SanitizedTagNode::class:
                    $transpiledView .= $this->transpileSanitizedTagNode($childNode);
                    $previousNodeWasExpression = false;

                    break;
                case UnsanitizedTagNode::class:
                    $transpiledView .= $this->transpileUnsanitizedTagNode($childNode);
                    $previousNodeWasExpression = false;

                    break;
                case CommentNode::class:
                    $transpiledView .= $this->transpileCommentNode($childNode);
                    $previousNodeWasExpression = false;

                    break;
                case ExpressionNode::class:
                    // To keep expressions from running against each other, we pad all expressions but the first
                    if ($previousNodeWasExpression) {
                        $transpiledView .= ' ';
                    }

                    $transpiledView .= $this->transpileExpressionNode($childNode);
                    $previousNodeWasExpression = true;

                    break;
                default:
                    throw new RuntimeException(
                        sprintf(
                            'Unknown node class %s',
                            get_class($childNode)
                        )
                    );
            }
        }

        return $transpiledView;
    }

    /**
     * Transpiles a sanitized tag node
     *
     * @param Node $node The node to transpile
     * @return string The transpiled node
     */
    protected function transpileSanitizedTagNode(Node $node) : string
    {
        $code = '';

        foreach ($node->getChildren() as $childNode) {
            $code .= '<?php echo $__opulenceFortuneTranspiler->sanitize(' . $childNode->getValue() . '); ?>';
        }

        return $code;
    }

    /**
     * Transpiles an unsanitized tag node
     *
     * @param Node $node The node to transpile
     * @return string The transpiled node
     */
    protected function transpileUnsanitizedTagNode(Node $node) : string
    {
        $code = '';

        foreach ($node->getChildren() as $childNode) {
            $code .= '<?php echo ' . $childNode->getValue() . '; ?>';
        }

        return $code;
    }
}
