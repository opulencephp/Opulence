<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Ioc;

/**
 * Tests the class binding
 */
class ClassBindingTest extends \PHPUnit\Framework\TestCase
{
    /** @var ClassBinding The binding to use in tests */
    private $binding = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->binding = new ClassBinding("foo", ["bar"]);
    }

    /**
     * Tests getting the concrete class
     */
    public function testGettingConcreteClass()
    {
        $this->assertEquals("foo", $this->binding->getConcreteClass());
    }

    /**
     * Tests getting the constructor primitives
     */
    public function testGettingConstructorPrimitives()
    {
        $this->assertEquals(["bar"], $this->binding->getConstructorPrimitives());
    }
}