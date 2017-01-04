<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Console\Responses\Compilers\Parsers;

use Opulence\Console\Responses\Compilers\Lexers\Tokens\Token;
use Opulence\Console\Responses\Compilers\Lexers\Tokens\TokenTypes;
use Opulence\Console\Responses\Compilers\Parsers\Nodes\TagNode;
use Opulence\Console\Responses\Compilers\Parsers\Nodes\WordNode;
use RuntimeException;

/**
 * Defines the response parser
 */
class Parser implements IParser
{
    /**
     * @inheritdoc
     * @param Token[] $tokens The list of tokens to parse
     */
    public function parse(array $tokens) : AbstractSyntaxTree
    {
        $ast = new AbstractSyntaxTree();

        foreach ($tokens as $token) {
            switch ($token->getType()) {
                case TokenTypes::T_WORD:
                    $ast->getCurrentNode()->addChild(new WordNode($token->getValue()));

                    break;
                case TokenTypes::T_TAG_OPEN:
                    $childNode = new TagNode($token->getValue());
                    $ast->getCurrentNode()->addChild($childNode);
                    $ast->setCurrentNode($childNode);

                    break;
                case TokenTypes::T_TAG_CLOSE:
                    if ($ast->getCurrentNode()->getValue() != $token->getValue()) {
                        throw new RuntimeException(
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
                case TokenTypes::T_EOF:
                    if (!$ast->getCurrentNode()->isRoot()) {
                        throw new RuntimeException(
                            sprintf(
                                "Unclosed %s \"%s\"",
                                $ast->getCurrentNode()->isTag() ? "tag" : "node",
                                $ast->getCurrentNode()->getValue()
                            )
                        );
                    }

                    break;
                default:
                    throw new RuntimeException(
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
