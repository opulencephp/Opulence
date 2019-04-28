<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Framework\Tests\Http;

use Opulence\Framework\Http\CsrfTokenChecker;
use Opulence\Http\Requests\Request;
use Opulence\Http\Requests\RequestHeaders;
use Opulence\Http\Requests\RequestMethods;
use Opulence\Sessions\ISession;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the CSRF token checker
 */
class CsrfTokenCheckerTest extends \PHPUnit\Framework\TestCase
{
    /** @var CsrfTokenChecker The token checker to use in tests */
    private $checker = null;
    /** @var Request|MockObject The request mock */
    private $request = null;
    /** @var ISession|MockObject The session mock */
    private $session = null;

    /**
     * Sets up the tests
     */
    protected function setUp() : void
    {
        $this->checker = new CsrfTokenChecker();
        $this->request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->session = $this->createMock(ISession::class);
    }

    /**
     * Tests checking an invalid token from the input
     */
    public function testCheckingInvalidTokenFromInput() : void
    {
        $this->session->expects($this->any())->method('get')->willReturn('foo');
        $this->request->expects($this->any())->method('getInput')->willReturn('bar');
        $this->assertFalse($this->checker->tokenIsValid($this->request, $this->session));
    }

    /**
     * Tests checking an invalid token from the X-CSRF header
     */
    public function testCheckingInvalidTokenFromXCSRFHeader() : void
    {
        $this->session->expects($this->any())->method('get')->willReturn('foo');
        $this->request->expects($this->any())->method('getInput')->willReturn(null);
        $mockHeaders = $this->createMock(RequestHeaders::class);
        $mockHeaders->expects($this->any())->method('get')->willReturn('bar');
        $this->request->expects($this->any())->method('getHeaders')->willReturn($mockHeaders);
        $this->assertFalse($this->checker->tokenIsValid($this->request, $this->session));
    }

    /**
     * Tests checking an invalid token from the X-XSRF header
     */
    public function testCheckingInvalidTokenFromXXsrfHeader() : void
    {
        $this->session->expects($this->any())->method('get')->willReturn('foo');
        $this->request->expects($this->any())->method('getInput')->willReturn(null);
        $mockHeaders = $this->createMock(RequestHeaders::class);
        $mockHeaders->expects($this->at(0))->method('get')->willReturn(null);
        $mockHeaders->expects($this->at(1))->method('get')->willReturn('bar');
        $this->request->expects($this->any())->method('getHeaders')->willReturn($mockHeaders);
        $this->assertFalse($this->checker->tokenIsValid($this->request, $this->session));
    }

    /**
     * Tests checking a valid token from the input
     */
    public function testCheckingValidTokenFromInput() : void
    {
        $this->session->expects($this->any())->method('get')->willReturn('foo');
        $this->request->expects($this->any())->method('getInput')->willReturn('foo');
        $this->assertTrue($this->checker->tokenIsValid($this->request, $this->session));
    }

    /**
     * Tests checking a valid token from the X-CSRF header
     */
    public function testCheckingValidTokenFromXCsrfHeader() : void
    {
        $this->session->expects($this->any())->method('get')->willReturn('foo');
        $this->request->expects($this->any())->method('getInput')->willReturn(null);
        $mockHeaders = $this->createMock(RequestHeaders::class);
        $mockHeaders->expects($this->any())->method('get')->willReturn('foo');
        $this->request->expects($this->any())->method('getHeaders')->willReturn($mockHeaders);
        $this->assertTrue($this->checker->tokenIsValid($this->request, $this->session));
    }

    /**
     * Tests checking a valid token from the X-XSRF header
     */
    public function testCheckingValidTokenFromXXsrfHeader() : void
    {
        $this->session->expects($this->any())->method('get')->willReturn('foo');
        $this->request->expects($this->any())->method('getInput')->willReturn(null);
        $mockHeaders = $this->createMock(RequestHeaders::class);
        $mockHeaders->expects($this->at(0))->method('get')->willReturn(null);
        $mockHeaders->expects($this->at(1))->method('get')->willReturn('foo');
        $this->request->expects($this->any())->method('getHeaders')->willReturn($mockHeaders);
        $this->assertTrue($this->checker->tokenIsValid($this->request, $this->session));
    }

    /**
     * Tests that the CSRF token is set in the session when it is not already there
     */
    public function testCsrfTokenIsSetInSessionWhenItIsNotAlreadyThere() : void
    {
        $this->session->expects($this->once())->method('get')->with(CsrfTokenChecker::TOKEN_INPUT_NAME)->willReturn('foo');
        $this->request->expects($this->once())->method('getInput')->willReturn('foo');
        $this->checker->tokenIsValid($this->request, $this->session);
    }

    /**
     * Tests that a null CSRF token returns false
     */
    public function testNullCsrfTokenReturnsFalse() : void
    {
        $this->assertFalse($this->checker->tokenIsValid($this->request, $this->session));
    }

    /**
     * Tests that the token is marked as valid for read HTTP GET method
     */
    public function testTokenIsValidForReadHttpGetMethod() : void
    {
        $this->request->expects($this->any())->method('getMethod')->willReturn(RequestMethods::GET);
        $this->checker->tokenIsValid($this->request, $this->session);
        // Essentially just test that we got here
        $this->assertTrue(true);
    }

    /**
     * Tests that the token is marked as valid for read HTTP HEAD method
     */
    public function testTokenIsValidForReadHttpHeadMethod() : void
    {
        $this->request->expects($this->any())->method('getMethod')->willReturn(RequestMethods::HEAD);
        $this->checker->tokenIsValid($this->request, $this->session);
        // Essentially just test that we got here
        $this->assertTrue(true);
    }

    /**
     * Tests that the token is marked as valid for read HTTP OPTIONS method
     */
    public function testTokenIsValidForReadHttpOptionsMethod() : void
    {
        $this->request->expects($this->any())->method('getMethod')->willReturn(RequestMethods::OPTIONS);
        $this->checker->tokenIsValid($this->request, $this->session);
        // Essentially just test that we got here
        $this->assertTrue(true);
    }
}
