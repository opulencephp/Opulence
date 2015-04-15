<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the session class
 */
namespace RDev\Sessions;
use RDev\Sessions\Ids\IdGenerator;
use RDev\Sessions\Ids\IIdGenerator;

class SessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests checking if an offset exists
     */
    public function testCheckingIfOffsetExists()
    {
        $session = new Session();
        $session["foo"] = "bar";
        $this->assertTrue(isset($session["foo"]));
        $this->assertFalse(isset($session["bar"]));
    }

    /**
     * Tests flushing the session
     */
    public function testFlushing()
    {
        $session = new Session();
        $session["foo"] = "bar";
        $session->flush();
        $this->assertFalse(isset($session["foo"]));
    }

    /**
     * Tests getting the Id
     */
    public function testGettingId()
    {
        $session = new Session(1, $this->getMock(IIdGenerator::class));
        $this->assertEquals(1, $session->getId());
    }

    /**
     * Tests getting a non-existent variable
     */
    public function testGettingNonExistentVariable()
    {
        $session = new Session();
        $this->assertNull($session["non-existent"]);
        $this->assertNull($session->get("non-existent"));
    }

    /**
     * Tests regenerating the session Id
     */
    public function testRegenerateId()
    {
        $idGenerator = $this->getMock(IIdGenerator::class);
        $idGenerator->expects($this->any())->method("generate")->willReturn("foobar");
        $session = new Session(null, $idGenerator);
        $session->regenerateId();
        $this->assertEquals("foobar", $session->getId());
    }

    /**
     * Tests regenerating the session Id with the default generator
     */
    public function testRegeneratingIdWithDefaultIdGenerator()
    {
        $session = new Session();
        $session->regenerateId();
        $this->assertTrue(is_string($session->getId()));
        $this->assertEquals(IdGenerator::DEFAULT_LENGTH, strlen($session->getId()));
    }

    /**
     * Tests setting the Id
     */
    public function testSettingId()
    {
        $session = new Session(1, $this->getMock(IIdGenerator::class));
        $session->setId(2);
        $this->assertEquals(2, $session->getId());
    }

    /**
     * Tests setting a null offset
     */
    public function testSettingNullOffset()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $session = new Session();
        $session[] = "foo";
    }

    /**
     * Tests setting the offset
     */
    public function testSettingOffset()
    {
        $session = new Session();
        $session["foo"] = "bar";
        $this->assertEquals("bar", $session["foo"]);
        $this->assertEquals("bar", $session->get("foo"));
    }

    /**
     * Tests setting a variable
     */
    public function testSettingVariable()
    {
        $session = new Session();
        $session->set("foo", "bar");
        $this->assertEquals("bar", $session->get("foo"));
    }

    /**
     * Tests starting a session
     */
    public function testStarting()
    {
        $variables = ["foo" => "bar", "baz" => "blah"];
        $session = new Session();
        $session->start($variables);
        $this->assertEquals("bar", $session["foo"]);
        $this->assertEquals("blah", $session["baz"]);
        $this->assertTrue($session->hasStarted());
    }

    /**
     * Tests that a session is not marked as started before it's started
     */
    public function testThatSessionNotMarkedAsStartedBeforeStarting()
    {
        $session = new Session();
        $this->assertFalse($session->hasStarted());
    }

    /**
     * Tests unsetting an offset
     */
    public function testUnsettingOffset()
    {
        $session = new Session();
        $session["foo"] = "bar";
        unset($session["foo"]);
        $this->assertNull($session["foo"]);
    }
} 