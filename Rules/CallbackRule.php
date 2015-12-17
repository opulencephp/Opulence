<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Validation\Rules;

/**
 * Defines the callback rule
 */
class CallbackRule implements IRule
{
    /** @var callable The callback to run */
    protected $callback = null;

    /**
     * @param callable $callback The callback to run
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = [])
    {
        return call_user_func($this->callback, $value, $allValues);
    }
}