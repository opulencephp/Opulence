<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Console\Responses\Compilers\Lexers\Tokens;

/**
 * Defines a response token
 */
class Token
{
    /** @var int The token type */
    private $type;
    /** @var mixed The value of the token */
    private $value;
    /** @var int The position of the token in the original text */
    private $position;

    /**
     * @param string $type The token type
     * @param mixed $value The value of the token
     * @param int $position The position of the token in the original text
     */
    public function __construct(string $type, $value, int $position)
    {
        $this->type = $type;
        $this->value = $value;
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
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
