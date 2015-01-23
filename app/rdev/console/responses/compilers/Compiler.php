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

    /**
     * @param Lexers\ILexer $lexer The lexer to use
     * @param Parsers\IParser $parser The parser to use
     */
    public function __construct(Lexers\ILexer $lexer, Parsers\IParser $parser)
    {
        $this->lexer = $lexer;
        $this->parser = $parser;
    }

    /**
     * {@inheritdoc}
     */
    public function compile($message, Elements\ElementRegistry $elementRegistry)
    {
        try
        {
            $tokens = $this->lexer->lex($message);
            $ast = $this->parser->parse($tokens);

            return $this->compileNode($ast->getRootNode(), $elementRegistry);
        }
        catch(\InvalidArgumentException $ex)
        {
            throw new \RuntimeException($ex->getMessage());
        }
    }

    /**
     * Recursively compiles a node and its children
     *
     * @param Nodes\Node $node The node to compile
     * @param Elements\ElementRegistry $elementRegistry The element registry
     * @return string The compiled node
     * @throws \RuntimeException Thrown if there was an error compiling the node
     * @throws \InvalidArgumentException Thrown if there is no matching element for a particular tag
     */
    private function compileNode(Nodes\Node $node, Elements\ElementRegistry $elementRegistry)
    {
        if($node->isLeaf())
        {
            // Don't compile a leaf that is a tag because that means it doesn't have any content
            if($node->isTag())
            {
                return "";
            }

            // Apply the parent's style
            return $node->getValue();
        }
        else
        {
            $output = "";

            foreach($node->getChildren() as $childNode)
            {
                if($node->isTag())
                {
                    $element = $elementRegistry->getElement($node->getValue());
                    $output .= $element->getStyle()->format($this->compileNode($childNode, $elementRegistry));
                }
                else
                {
                    $output .= $this->compileNode($childNode, $elementRegistry);
                }
            }

            return $output;
        }
    }
}