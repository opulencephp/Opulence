<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Framework\Tests\Http\Testing\PhpUnit\Assertions;

use LogicException;
use Opulence\Framework\Http\Testing\PhpUnit\Assertions\ViewAssertions;
use Opulence\Routing\Controller;
use Opulence\Views\IView;
use stdClass;

/**
 * Tests the view assertions
 */
class ViewAssertionsTest extends \PHPUnit\Framework\TestCase
{
    /** @var ViewAssertions The assertions to use in tests */
    private $assertions = null;
    /** @var IView|\PHPUnit_Framework_MockObject_MockObject The view to use in tests */
    private $mockView = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->assertions = new ViewAssertions();
        $this->mockView = $this->createMock(IView::class);
    }

    /**
     * Tests asserting that a view has a variable
     */
    public function testAssertViewHasVariable()
    {
        $this->mockView->expects($this->any())
            ->method('getVar')
            ->with('foo')
            ->willReturn('bar');
        $controller = $this->createMock(Controller::class);
        $controller->expects($this->exactly(2))
            ->method('getView')
            ->willReturn($this->mockView);
        $this->assertions->setController($controller);
        $this->assertSame($this->assertions, $this->assertions->hasVar('foo'));
        $this->assertSame($this->assertions, $this->assertions->varEquals('foo', 'bar'));
    }

    /**
     * Tests that a logic exception is thrown if checking if a view has a variable when using a non-Opulence controller
     */
    public function testLogicExceptionCheckingIfViewHasVariableFromNonOpulenceController()
    {
        $this->expectException(LogicException::class);
        $this->assertions->setController(new stdClass());
        $this->assertions->hasVar('foo');
    }

    /**
     * Tests that a logic exception is thrown if getting a view variable when using a non-Opulence controller
     */
    public function testLogicExceptionGettingViewVariableFromNonOpulenceController()
    {
        $this->expectException(LogicException::class);
        $this->assertions->setController(new stdClass());
        $this->assertions->varEquals('bar', 'foo');
    }
}
