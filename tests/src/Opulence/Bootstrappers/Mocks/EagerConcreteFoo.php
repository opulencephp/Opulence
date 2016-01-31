<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Bootstrappers\Mocks;

/**
 * Defines a class that implement the foo interface
 */
class EagerConcreteFoo implements EagerFooInterface
{
    /**
     * @inheritdoc
     */
    public function getClass() : string
    {
        return self::class;
    }
}