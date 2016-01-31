<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Views\Compilers\Fortune\Parsers;

use Opulence\Views\Compilers\Fortune\Lexers\Tokens\Token;
use Opulence\Views\Compilers\Fortune\Lexers\Tokens\TokenTypes;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\CommentNode;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\DirectiveNameNode;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\DirectiveNode;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\ExpressionNode;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\SanitizedTagNode;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\UnsanitizedTagNode;
use RuntimeException;

/**
 * Defines a view parser
 */
class Parser implements IParser
{
    /**
     * @inheritdoc
     */
    public function parse(array $tokens) : AbstractSyntaxTree
    {
        $ast = new AbstractSyntaxTree();

        /** @var Token $token */
        foreach ($tokens as $token) {
            switch ($token->getType()) {
                case TokenTypes::T_EXPRESSION:
                    $ast->getCurrentNode()->addChild(new ExpressionNode($token->getValue()));

                    break;
                case TokenTypes::T_DIRECTIVE_OPEN:
                    if (!$ast->getCurrentNode()->isRoot()) {
                        $this->throwImproperlyNestedNodeException($token);
                    }

                    $childNode = new DirectiveNode();
                    $ast->getCurrentNode()->addChild($childNode);
                    $ast->setCurrentNode($childNode);

                    break;
                case TokenTypes::T_DIRECTIVE_CLOSE:
                    if (!$ast->getCurrentNode()->isDirective()) {
                        $this->throwUnopenedDelimiterException($token);
                    }

                    $ast->setCurrentNode($ast->getCurrentNode()->getParent());

                    break;
                case TokenTypes::T_DIRECTIVE_NAME:
                    $ast->getCurrentNode()->addChild(new DirectiveNameNode($token->getValue()));

                    break;
                case TokenTypes::T_SANITIZED_TAG_OPEN:
                    if (!$ast->getCurrentNode()->isRoot()) {
                        $this->throwImproperlyNestedNodeException($token);
                    }

                    $childNode = new SanitizedTagNode();
                    $ast->getCurrentNode()->addChild($childNode);
                    $ast->setCurrentNode($childNode);

                    break;
                case TokenTypes::T_SANITIZED_TAG_CLOSE:
                    if (!$ast->getCurrentNode()->isSanitizedTag()) {
                        $this->throwUnopenedDelimiterException($token);
                    }

                    $ast->setCurrentNode($ast->getCurrentNode()->getParent());

                    break;
                case TokenTypes::T_UNSANITIZED_TAG_OPEN:
                    if (!$ast->getCurrentNode()->isRoot()) {
                        $this->throwImproperlyNestedNodeException($token);
                    }

                    $childNode = new UnsanitizedTagNode();
                    $ast->getCurrentNode()->addChild($childNode);
                    $ast->setCurrentNode($childNode);

                    break;
                case TokenTypes::T_UNSANITIZED_TAG_CLOSE:
                    if (!$ast->getCurrentNode()->isUnsanitizedTag()) {
                        $this->throwUnopenedDelimiterException($token);
                    }

                    $ast->setCurrentNode($ast->getCurrentNode()->getParent());

                    break;
                case TokenTypes::T_COMMENT_OPEN:
                    if (!$ast->getCurrentNode()->isRoot()) {
                        $this->throwImproperlyNestedNodeException($token);
                    }

                    $childNode = new CommentNode();
                    $ast->getCurrentNode()->addChild($childNode);
                    $ast->setCurrentNode($childNode);

                    break;
                case TokenTypes::T_COMMENT_CLOSE:
                    if (!$ast->getCurrentNode()->isComment()) {
                        $this->throwUnopenedDelimiterException($token);
                    }

                    $ast->setCurrentNode($ast->getCurrentNode()->getParent());

                    break;
                case TokenTypes::T_PHP_TAG_OPEN:
                    $ast->getCurrentNode()->addChild(new ExpressionNode($token->getValue()));

                    break;
                case TokenTypes::T_PHP_TAG_CLOSE:
                    $ast->getCurrentNode()->addChild(new ExpressionNode($token->getValue()));

                    break;
                default:
                    throw new RuntimeException(
                        sprintf(
                            'Unknown token type "%s" with value "%s" near line %d',
                            $token->getType(),
                            $token->getValue(),
                            $token->getLine()
                        )
                    );
            }
        }

        if (!$ast->getCurrentNode()->isRoot()) {
            throw new RuntimeException(
                sprintf(
                    "Expected close delimiter, found %s",
                    $ast->getCurrentNode()->getValue()
                )
            );
        }

        return $ast;
    }

    /**
     * Throws an exception for an improperly nested node
     *
     * @param Token $token The invalid token
     * @throws RuntimeException Always thrown
     */
    private function throwImproperlyNestedNodeException(Token $token)
    {
        throw new RuntimeException(
            sprintf(
                "Nesting statements of type %s not allowed near line %d",
                $token->getType(),
                $token->getLine()
            )
        );
    }

    /**
     * Throws an exception for an unopened delimiter
     *
     * @param Token $token The invalid token
     * @throws RuntimeException Always thrown
     */
    private function throwUnopenedDelimiterException(Token $token)
    {
        throw new RuntimeException(
            sprintf(
                "Unopened %s near line %d",
                $token->getType(),
                $token->getLine()
            )
        );
    }
}