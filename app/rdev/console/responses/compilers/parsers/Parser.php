<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the response parser
 */
namespace RDev\Console\Responses\Compilers\Parsers;
use RDev\Console\Responses\Compilers\Nodes;
use RDev\Console\Responses\Compilers\Tokens;

class Parser implements IParser
{
    /**
     * {@inheritdoc}
     * @param Tokens\Token[] $tokens The list of tokens to parse
     */
    public function parse(array $tokens)
    {
        $ast = new AbstractSyntaxTree();

        foreach($tokens as $token)
        {
            switch($token->getType())
            {
                case Tokens\TokenTypes::T_WORD:
                    $ast->getCurrentNode()->addChild(new Nodes\WordNode($token->getValue()));

                    break;
                case Tokens\TokenTypes::T_TAG_OPEN:
                    $childNode = new Nodes\TagNode($token->getValue());
                    $ast->getCurrentNode()->addChild($childNode);
                    $ast->setCurrentNode($childNode);

                    break;
                case Tokens\TokenTypes::T_TAG_CLOSE:
                    if($ast->getCurrentNode()->getValue() != $token->getValue())
                    {
                        throw new \RuntimeException(
                            sprintf(
                                "Improperly nested tag \"%s\" near character #%d",
                                $token->getValue(),
                                $token->getPosition()
                            )
                        );
                    }

                    // Move up one in the tree
                    $ast->setCurrentNode($ast->getCurrentNode()->getParent());

                    break;
                case Tokens\TokenTypes::T_EOF:
                    if(!$ast->getCurrentNode()->isRoot())
                    {
                        throw new \RuntimeException(
                            sprintf(
                                "Unclosed %s \"%s\"",
                                $ast->getCurrentNode()->isTag() ? "tag" : "node",
                                $ast->getCurrentNode()->getValue()
                            )
                        );
                    }

                    break;
                default:
                    throw new \RuntimeException(
                        sprintf(
                            "Unknown token type \"%s\" with value \"%s\" near character #%d",
                            $token->getType(),
                            $token->getValue(),
                            $token->getPosition()
                        )
                    );
            }
        }

        return $ast;
    }
}