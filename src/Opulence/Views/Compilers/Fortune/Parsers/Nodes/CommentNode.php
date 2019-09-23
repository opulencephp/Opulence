<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Views\Compilers\Fortune\Parsers\Nodes;

/**
 * Defines a comment node
 */
final class CommentNode extends Node
{
    /**
     * @inheritdoc
     */
    public function isComment(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isDirective(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isDirectiveName(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isExpression(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isSanitizedTag(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isUnsanitizedTag(): bool
    {
        return false;
    }
}
