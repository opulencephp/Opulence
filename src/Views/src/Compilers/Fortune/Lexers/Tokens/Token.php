<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Views\Compilers\Fortune\Lexers\Tokens;

/**
 * Defines a view token
 */
class Token
{
    /** @var string The token type */
    private string $type;
    /** @var mixed The value of the token */
    private $value;
    /** @var int The line the token is on */
    private int $line;

    /**
     * @param string $type The token type
     * @param mixed $value The value of the token
     * @param int $line The line the token is on
     */
    public function __construct(string $type, $value, int $line)
    {
        $this->type = $type;
        $this->value = $value;
        $this->line = $line;
    }

    /**
     * @return int
     */
    public function getLine(): int
    {
        return $this->line;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
