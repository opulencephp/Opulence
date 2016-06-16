<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Events;

/**
 * Tests the event class
 */
class EventTest extends \PHPUnit\Framework\TestCase
{
    /** @var Event The event to test */
    private $event = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->event = $this->getMockBuilder(Event::class)
            ->setMethods(null)
            ->getMock();
    }

    /**
     * Tests that the propagation is not stopped by default
     */
    public function testPropagationIsNotStoppedByDefault()
    {
        $this->assertFalse($this->event->propagationIsStopped());
    }

    /**
     * Tests stopping the propagation
     */
    public function testStoppingPropagation()
    {
        $this->event->stopPropagation();
        $this->assertTrue($this->event->propagationIsStopped());
    }
}