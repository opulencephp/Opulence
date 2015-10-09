<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines an element compiler
 */
namespace Opulence\Console\Responses\Compilers;

use InvalidArgumentException;
use Opulence\Console\Responses\Compilers\Lexers\ILexer;
use Opulence\Console\Responses\Compilers\Parsers\IParser;
use Opulence\Console\Responses\Compilers\Parsers\Nodes\Node;
use Opulence\Console\Responses\Formatters\Elements\Colors;
use Opulence\Console\Responses\Formatters\Elements\Element;
use Opulence\Console\Responses\Formatters\Elements\ElementCollection;
use Opulence\Console\Responses\Formatters\Elements\Style;
use Opulence\Console\Responses\Formatters\Elements\TextStyles;
use RuntimeException;

class Compiler implements ICompiler
{
    /** @var ILexer The lexer to use */
    private $lexer = null;
    /** @var IParser The parser to use */
    private $parser = null;
    /** @var ElementCollection The list of elements registered to the compiler */
    private $elements = null;
    /** @var bool Whether or not messages should be styled */
    private $isStyled = true;

    /**
     * @param ILexer $lexer The lexer to use
     * @param IParser $parser The parser to use
     */
    public function __construct(ILexer $lexer, IParser $parser)
    {
        $this->lexer = $lexer;
        $this->parser = $parser;
        // Register the built-in elements
        $this->elements = new ElementCollection();
        $this->elements->add([
            new Element("success", new Style(Colors::BLACK, Colors::GREEN)),
            new Element("info", new Style(Colors::GREEN)),
            new Element("error", new Style(Colors::BLACK, Colors::YELLOW)),
            new Element("fatal", new Style(Colors::WHITE, Colors::RED)),
            new Element("question", new Style(Colors::WHITE, Colors::BLUE)),
            new Element("comment", new Style(Colors::YELLOW)),
            new Element("b", new Style(null, null, [TextStyles::BOLD])),
            new Element("u", new Style(null, null, [TextStyles::UNDERLINE]))
        ]);
    }

    /**
     * @inheritdoc
     */
    public function compile($message)
    {
        if (!$this->isStyled) {
            return strip_tags($message);
        }

        try {
            $tokens = $this->lexer->lex($message);
            $ast = $this->parser->parse($tokens);

            return $this->compileNode($ast->getRootNode());
        } catch (InvalidArgumentException $ex) {
            throw new RuntimeException($ex->getMessage());
        }
    }

    /**
     * @return ElementCollection
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * @inheritdoc
     */
    public function setStyled($isStyled)
    {
        $this->isStyled = $isStyled;
    }

    /**
     * Recursively compiles a node and its children
     *
     * @param Node $node The node to compile
     * @return string The compiled node
     * @throws RuntimeException Thrown if there was an error compiling the node
     * @throws InvalidArgumentException Thrown if there is no matching element for a particular tag
     */
    private function compileNode(Node $node)
    {
        if ($node->isLeaf()) {
            // Don't compile a leaf that is a tag because that means it doesn't have any content
            if ($node->isTag()) {
                return "";
            }

            return $node->getValue();
        } else {
            $output = "";

            foreach ($node->getChildren() as $childNode) {
                if ($node->isTag()) {
                    $element = $this->elements->getElement($node->getValue());
                    $output .= $element->getStyle()->format($this->compileNode($childNode));
                } else {
                    $output .= $this->compileNode($childNode);
                }
            }

            return $output;
        }
    }
}