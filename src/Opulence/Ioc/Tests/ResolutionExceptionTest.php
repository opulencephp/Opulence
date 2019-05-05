<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Ioc\Tests;

use Opulence\Ioc\ResolutionException;
use PHPUnit\Framework\TestCase;

/**
 * Tests the resolution exception
 */
class ResolutionExceptionTest extends TestCase
{
    public function testGetInterfaceReturnsInterfaceInjectedInConstructor(): void
    {
        $exception = new ResolutionException('foo', null);
        $this->assertEquals('foo', $exception->getInterface());
    }

    public function testGetTargetClassReturnsTargetClassInjectedInConstructor(): void
    {
        $exception = new ResolutionException('foo', 'bar');
        $this->assertEquals('bar', $exception->getTargetClass());
    }
}
