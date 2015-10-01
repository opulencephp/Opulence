<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the CSRF token checker
 */
namespace Opulence\Framework\HTTP;

use Opulence\Cryptography\Utilities\Strings;
use Opulence\HTTP\Headers;
use Opulence\HTTP\Requests\Request;
use Opulence\Sessions\ISession;

class CSRFTokenCheckerTest extends \PHPUnit_Framework_TestCase
{
    /** @var CSRFTokenChecker The token checker to use in tests */
    private $checker = null;
    /** @var Strings|\PHPUnit_Framework_MockObject_MockObject The string utility */
    private $strings = null;
    /** @var Request|\PHPUnit_Framework_MockObject_MockObject The request mock */
    private $request = null;
    /** @var ISession|\PHPUnit_Framework_MockObject_MockObject The session mock */
    private $session = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->strings = $this->getMock(Strings::class);
        $this->checker = new CSRFTokenChecker($this->strings);
        $this->request = $this->getMock(Request::class, [], [], "", false);
        $this->session = $this->getMock(ISession::class);
    }

    /**
     * Tests that the CSRF token is set in the session when it is not already there
     */
    public function testCSRFTokenIsSetInSessionWhenItIsNotAlreadyThere()
    {
        $this->strings->expects($this->once())->method("generateRandomString")->willReturn("foo");
        $this->session->expects($this->once())->method("set")->with(CSRFTokenChecker::TOKEN_INPUT_NAME, "foo");
        $this->request->expects($this->once())->method("getInput")->willReturn("foo");
        $this->checker->tokenIsValid($this->request, $this->session);
    }

    /**
     * Tests checking an invalid token from the input
     */
    public function testCheckingInvalidTokenFromInput()
    {
        $this->session->expects($this->any())->method("get")->willReturn("foo");
        $this->request->expects($this->any())->method("getInput")->willReturn("bar");
        $this->strings->expects($this->any())->method("isEqual")->with("foo", "bar")->willReturn(false);
        $this->assertFalse($this->checker->tokenIsValid($this->request, $this->session));
    }

    /**
     * Tests checking an invalid token from the X-CSRF header
     */
    public function testCheckingInvalidTokenFromXCSRFHeader()
    {
        $this->session->expects($this->any())->method("get")->willReturn("foo");
        $this->request->expects($this->any())->method("getInput")->willReturn(null);
        $mockHeaders = $this->getMock(Headers::class);
        $mockHeaders->expects($this->any())->method("get")->willReturn("bar");
        $this->request->expects($this->any())->method("getHeaders")->willReturn($mockHeaders);
        $this->strings->expects($this->any())->method("isEqual")->with("foo", "bar")->willReturn(false);
        $this->assertFalse($this->checker->tokenIsValid($this->request, $this->session));
    }

    /**
     * Tests checking an invalid token from the X-XSRF header
     */
    public function testCheckingInvalidTokenFromXXSRFHeader()
    {
        $this->session->expects($this->any())->method("get")->willReturn("foo");
        $this->request->expects($this->any())->method("getInput")->willReturn(null);
        $mockHeaders = $this->getMock(Headers::class);
        $mockHeaders->expects($this->at(0))->method("get")->willReturn(null);
        $mockHeaders->expects($this->at(1))->method("get")->willReturn("bar");
        $this->request->expects($this->any())->method("getHeaders")->willReturn($mockHeaders);
        $this->strings->expects($this->any())->method("isEqual")->with("foo", "bar")->willReturn(false);
        $this->assertFalse($this->checker->tokenIsValid($this->request, $this->session));
    }

    /**
     * Tests checking a valid token from the input
     */
    public function testCheckingValidTokenFromInput()
    {
        $this->session->expects($this->any())->method("get")->willReturn("foo");
        $this->request->expects($this->any())->method("getInput")->willReturn("foo");
        $this->strings->expects($this->any())->method("isEqual")->willReturn(true);
        $this->strings->expects($this->any())->method("isEqual")->with("foo", "foo")->willReturn(true);
        $this->assertTrue($this->checker->tokenIsValid($this->request, $this->session));
    }

    /**
     * Tests checking a valid token from the X-CSRF header
     */
    public function testCheckingValidTokenFromXCSRFHeader()
    {
        $this->session->expects($this->any())->method("get")->willReturn("foo");
        $this->request->expects($this->any())->method("getInput")->willReturn(null);
        $mockHeaders = $this->getMock(Headers::class);
        $mockHeaders->expects($this->any())->method("get")->willReturn("foo");
        $this->request->expects($this->any())->method("getHeaders")->willReturn($mockHeaders);
        $this->strings->expects($this->any())->method("isEqual")->with("foo", "foo")->willReturn(true);
        $this->assertTrue($this->checker->tokenIsValid($this->request, $this->session));
    }

    /**
     * Tests checking a valid token from the X-XSRF header
     */
    public function testCheckingValidTokenFromXXSRFHeader()
    {
        $this->session->expects($this->any())->method("get")->willReturn("foo");
        $this->request->expects($this->any())->method("getInput")->willReturn(null);
        $mockHeaders = $this->getMock(Headers::class);
        $mockHeaders->expects($this->at(0))->method("get")->willReturn(null);
        $mockHeaders->expects($this->at(1))->method("get")->willReturn("foo");
        $this->request->expects($this->any())->method("getHeaders")->willReturn($mockHeaders);
        $this->strings->expects($this->any())->method("isEqual")->with("foo", "foo")->willReturn(true);
        $this->assertTrue($this->checker->tokenIsValid($this->request, $this->session));
    }

    /**
     * Tests that the token is marked as valid for read HTTP GET method
     */
    public function testTokenIsValidForReadHTTGETPMethod()
    {
        $this->request->expects($this->any())->method("getMethod")->willReturn(Request::METHOD_GET);
        $this->checker->tokenIsValid($this->request, $this->session);
    }

    /**
     * Tests that the token is marked as valid for read HTTP HEAD method
     */
    public function testTokenIsValidForReadHTTHEADPMethod()
    {
        $this->request->expects($this->any())->method("getMethod")->willReturn(Request::METHOD_HEAD);
        $this->checker->tokenIsValid($this->request, $this->session);
    }

    /**
     * Tests that the token is marked as valid for read HTTP OPTIONS method
     */
    public function testTokenIsValidForReadHTTOPTIONSPMethod()
    {
        $this->request->expects($this->any())->method("getMethod")->willReturn(Request::METHOD_OPTIONS);
        $this->checker->tokenIsValid($this->request, $this->session);
    }
}