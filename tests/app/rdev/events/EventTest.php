<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the event class
 */
namespace RDev\Events;

class EventTest extends \PHPUnit_Framework_TestCase
{
    /** @var Event The event to test */
    private $event = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->event = $this->getMockForAbstractClass(Event::class);
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