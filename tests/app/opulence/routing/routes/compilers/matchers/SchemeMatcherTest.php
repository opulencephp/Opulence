<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the scheme matcher
 */
namespace Opulence\Routing\Routes\Compilers\Matchers;

use Opulence\HTTP\Requests\Request;
use Opulence\Routing\Routes\ParsedRoute;

class SchemeMatcherTest extends \PHPUnit_Framework_TestCase
{
    /** @var SchemeMatcher The matcher to use in tests */
    private $matcher = null;
    /** @var Request|\PHPUnit_Framework_MockObject_MockObject The request to use in tests */
    private $request = null;
    /** @var ParsedRoute|\PHPUnit_Framework_MockObject_MockObject The route to use in tests */
    private $route = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->matcher = new SchemeMatcher();
        $this->request = $this->getMock(Request::class, [], [], "", false);
        $this->route = $this->getMock(ParsedRoute::class, [], [], "", false);
    }

    /**
     * Tests that there is a match when on an HTTPS scheme with a secure route
     */
    public function testMatchOnHTTPSWithSecureRoute()
    {
        $this->route->expects($this->any())->method("isSecure")->willReturn(true);
        $this->request->expects($this->any())->method("isSecure")->willReturn(true);
        $this->assertTrue($this->matcher->isMatch($this->route, $this->request));
    }

    /**
     * Tests that there is a match when on an HTTP scheme with an insecure route
     */
    public function testMatchOnHTTPWithInsecureRoute()
    {
        $this->route->expects($this->any())->method("isSecure")->willReturn(false);
        $this->request->expects($this->any())->method("isSecure")->willReturn(false);
        $this->assertTrue($this->matcher->isMatch($this->route, $this->request));
    }

    /**
     * Tests that there is no match when on an HTTP scheme with a secure route
     */
    public function testNoMatchOnHTTPWithSecureRoute()
    {
        $this->route->expects($this->any())->method("isSecure")->willReturn(true);
        $this->request->expects($this->any())->method("isSecure")->willReturn(false);
        $this->assertFalse($this->matcher->isMatch($this->route, $this->request));
    }
}