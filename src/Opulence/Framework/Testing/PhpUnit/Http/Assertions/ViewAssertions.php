<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Testing\PhpUnit\Http\Assertions;

use LogicException;
use Opulence\Routing\Controller;
use PHPUnit_Framework_TestCase;

/**
 * Defines the HTTP view assertions
 */
class ViewAssertions extends PHPUnit_Framework_TestCase
{
    /** @var Controller|mixed The matched controller */
    protected $controller = null;

    /**
     * Asserts that the view has a variable
     *
     * @param string $name The name of the variable to search for
     * @return self For method chaining
     * @throws LogicException Thrown if the controller does not extend the base controller
     */
    public function hasVar(string $name) : self
    {
        $this->checkControllerSet();

        if (!$this->controller instanceof Controller) {
            throw new LogicException("Controller does not extend " . Controller::class);
        }

        $this->assertNotNull($this->controller->getView()->getVar($name));

        return $this;
    }

    /**
     * @param Controller|mixed $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * Asserts that the view has a variable with a certain value
     *
     * @param string $name The name of the tag to search for
     * @param mixed $expected The expected value
     * @return self For method chaining
     * @throws LogicException Thrown if the controller does not extend the base controller
     */
    public function varEquals(string $name, $expected) : self
    {
        $this->checkControllerSet();

        if (!$this->controller instanceof Controller) {
            throw new LogicException("Controller does not extend " . Controller::class);
        }

        $this->assertEquals($expected, $this->controller->getView()->getVar($name));

        return $this;
    }

    /**
     * Checks if the controller was set
     * Useful for making sure the controller was set before making any assertions on it
     */
    private function checkControllerSet()
    {
        if ($this->controller === null) {
            $this->fail("Must call route() before assertions");
        }
    }
}