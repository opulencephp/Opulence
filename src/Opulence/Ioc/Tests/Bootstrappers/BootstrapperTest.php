<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Tests\Bootstrappers;

use BadMethodCallException;
use Opulence\Ioc\Tests\Bootstrappers\Mocks\Bootstrapper;

/**
 * Tests the bootstrapper
 */
class BootstrapperTest extends \PHPUnit\Framework\TestCase
{
    /** @var Bootstrapper The bootstrapper to use in tests */
    private $bootstrapper = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->bootstrapper = new Bootstrapper();
    }

    /**
     * Tests calling a bad method
     */
    public function testCallingBadMethod()
    {
        $this->expectException(BadMethodCallException::class);
        $this->bootstrapper->foo('bar');
    }
}
