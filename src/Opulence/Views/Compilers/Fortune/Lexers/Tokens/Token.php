<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Views\Compilers\Fortune\Lexers\Tokens;

/**
 * Defines a view token
 */
class Token
{
    /** @var int The token type */
    private $type = null;
    /** @var mixed The value of the token */
    private $value = null;
    /** @var int The line the token is on */
    private $line = 0;

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
    public function getLine() : int
    {
        return $this->line;
    }

    /**
     * @return string
     */
    public function getType() : string
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
