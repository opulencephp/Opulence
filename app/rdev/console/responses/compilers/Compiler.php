<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines an element compiler
 */
namespace RDev\Console\Responses\Compilers;
use RDev\Console\Responses\Formatters\Elements;

class Compiler implements ICompiler
{
    /** @var Lexers\ILexer The lexer to use */
    private $lexer = null;
    /** @var Parsers\IParser The parser to use */
    private $parser = null;
    /** @var Elements\Elements The list of elements registered to the compiler */
    private $elements = null;

    /**
     * @param Lexers\ILexer $lexer The lexer to use
     * @param Parsers\IParser $parser The parser to use
     */
    public function __construct(Lexers\ILexer $lexer, Parsers\IParser $parser)
    {
        $this->lexer = $lexer;
        $this->parser = $parser;
        // Register the built-in elements
        $this->elements = new Elements\Elements();
        $this->elements->add([
            new Elements\Element("info", new Elements\Style(Elements\Colors::GREEN)),
            new Elements\Element("error", new Elements\Style(Elements\Colors::BLACK, Elements\Colors::YELLOW)),
            new Elements\Element("fatal", new Elements\Style(Elements\Colors::WHITE, Elements\Colors::RED)),
            new Elements\Element("question", new Elements\Style(Elements\Colors::WHITE, Elements\Colors::BLUE)),
            new Elements\Element("comment", new Elements\Style(Elements\Colors::YELLOW)),
            new Elements\Element("b", new Elements\Style(null, null, [Elements\TextStyles::BOLD])),
            new Elements\Element("u", new Elements\Style(null, null, [Elements\TextStyles::UNDERLINE]))
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function compile($message)
    {
        try
        {
            $tokens = $this->lexer->lex($message);
            $ast = $this->parser->parse($tokens);

            return $this->compileNode($ast->getRootNode());
        }
        catch(\InvalidArgumentException $ex)
        {
            throw new \RuntimeException($ex->getMessage());
        }
    }

    /**
     * @return Elements\Elements
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * Recursively compiles a node and its children
     *
     * @param Nodes\Node $node The node to compile
     * @return string The compiled node
     * @throws \RuntimeException Thrown if there was an error compiling the node
     * @throws \InvalidArgumentException Thrown if there is no matching element for a particular tag
     */
    private function compileNode(Nodes\Node $node)
    {
        if($node->isLeaf())
        {
            // Don't compile a leaf that is a tag because that means it doesn't have any content
            if($node->isTag())
            {
                return "";
            }

            return $node->getValue();
        }
        else
        {
            $output = "";

            foreach($node->getChildren() as $childNode)
            {
                if($node->isTag())
                {
                    $element = $this->elements->getElement($node->getValue());
                    $output .= $element->getStyle()->format($this->compileNode($childNode));
                }
                else
                {
                    $output .= $this->compileNode($childNode);
                }
            }

            return $output;
        }
    }
}