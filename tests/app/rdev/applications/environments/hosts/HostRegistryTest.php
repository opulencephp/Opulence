<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the host registry
 */
namespace RDev\Applications\Environments\Hosts;

class HostRegistryTest extends \PHPUnit_Framework_TestCase
{
    /** @var HostRegistry The registry to use in tests */
    private $registry = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->registry = new HostRegistry();
    }

    /**
     * Tests getting the hosts when there are none
     */
    public function testGettingHostsWhenThereAreNone()
    {
        $this->assertEquals([], $this->registry->getHosts());
    }

    /**
     * Tests registering an array of hosts
     */
    public function testRegisteringArrayOfHosts()
    {
        $host1 = $this->getMock(Host::class, [], [], "", false);
        $host2 = $this->getMock(Host::class, [], [], "", false);
        $host3 = $this->getMock(Host::class, [], [], "", false);
        $this->registry->registerHost("foo", [$host1, $host2, $host3]);
        $this->assertEquals(["foo" => [$host1, $host2, $host3]], $this->registry->getHosts());
    }

    /**
     * Tests registering an empty array
     */
    public function testRegisteringEmptyArray()
    {
        $this->registry->registerHost("foo", []);
        $this->assertEquals(["foo" => []], $this->registry->getHosts());
    }

    /**
     * Tests registering a host
     */
    public function testRegisteringHost()
    {
        $host = $this->getMock(Host::class, [], [], "", false);
        $this->registry->registerHost("foo", $host);
        $this->assertEquals(["foo" => [$host]], $this->registry->getHosts());
    }

    /**
     * Tests registering multiple arrays of hosts
     */
    public function testRegisteringMultipleArraysOfHosts()
    {
        $host1 = $this->getMock(Host::class, [], [], "", false);
        $host2 = $this->getMock(Host::class, [], [], "", false);
        $this->registry->registerHost("foo", [$host1]);
        $this->registry->registerHost("foo", [$host2]);
        $this->assertEquals(["foo" => [$host1, $host2]], $this->registry->getHosts());
    }

    /**
     * Tests registering multiple hosts
     */
    public function testRegisteringMultipleHosts()
    {
        $host1 = $this->getMock(Host::class, [], [], "", false);
        $host2 = $this->getMock(Host::class, [], [], "", false);
        $host3 = $this->getMock(Host::class, [], [], "", false);
        $this->registry->registerHost("foo", $host1);
        $this->registry->registerHost("foo", $host2);
        $this->registry->registerHost("bar", $host3);
        $this->assertEquals(["foo" => [$host1, $host2], "bar" => [$host3]], $this->registry->getHosts());
    }
}