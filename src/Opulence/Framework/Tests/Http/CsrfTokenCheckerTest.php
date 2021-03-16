<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Framework\Tests\Http;

use Opulence\Framework\Http\CsrfTokenChecker;
use Opulence\Http\Requests\Request;
use Opulence\Http\Requests\RequestHeaders;
use Opulence\Http\Requests\RequestMethods;
use Opulence\Sessions\ISession;

/**
 * Tests the CSRF token checker
 */
class CsrfTokenCheckerTest extends \PHPUnit\Framework\TestCase
{
    /** @var CsrfTokenChecker The token checker to use in tests */
    private $checker = null;
    /** @var Request|\PHPUnit_Framework_MockObject_MockObject The request mock */
    private $request = null;
    /** @var ISession|\PHPUnit_Framework_MockObject_MockObject The session mock */
    private $session = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
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
    public function testCheckingInvalidTokenFromInput()
    {
        $this->session->expects($this->any())->method('get')->willReturn('foo');
        $this->request->expects($this->any())->method('getInput')->willReturn('bar');
        $this->assertFalse($this->checker->tokenIsValid($this->request, $this->session));
    }

    /**
     * Tests checking an invalid token from the X-CSRF header
     */
    public function testCheckingInvalidTokenFromXCSRFHeader()
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
    public function testCheckingInvalidTokenFromXXsrfHeader()
    {
        $this->session->expects($this->any())->method('get')->willReturn('foo');
        $this->request->expects($this->any())->method('getInput')->willReturn(null);
        $mockHeaders = $this->createMock(RequestHeaders::class);
        $mockHeaders->expects($this->exactly(2))->method('get')->willReturnOnConsecutiveCalls(null, 'bar');
        $this->request->expects($this->any())->method('getHeaders')->willReturn($mockHeaders);
        $this->assertFalse($this->checker->tokenIsValid($this->request, $this->session));
    }

    /**
     * Tests checking a valid token from the input
     */
    public function testCheckingValidTokenFromInput()
    {
        $this->session->expects($this->any())->method('get')->willReturn('foo');
        $this->request->expects($this->any())->method('getInput')->willReturn('foo');
        $this->assertTrue($this->checker->tokenIsValid($this->request, $this->session));
    }

    /**
     * Tests checking a valid token from the X-CSRF header
     */
    public function testCheckingValidTokenFromXCsrfHeader()
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
    public function testCheckingValidTokenFromXXsrfHeader()
    {
        $this->session->expects($this->any())->method('get')->willReturn('foo');
        $this->request->expects($this->any())->method('getInput')->willReturn(null);
        $mockHeaders = $this->createMock(RequestHeaders::class);
        $mockHeaders->expects($this->exactly(2))->method('get')->willReturn(null, 'foo');
        $this->request->expects($this->any())->method('getHeaders')->willReturn($mockHeaders);
        $this->assertTrue($this->checker->tokenIsValid($this->request, $this->session));
    }

    /**
     * Tests that the CSRF token is set in the session when it is not already there
     */
    public function testCsrfTokenIsSetInSessionWhenItIsNotAlreadyThere()
    {
        $this->session->expects($this->once())->method('get')->with(CsrfTokenChecker::TOKEN_INPUT_NAME)->willReturn('foo');
        $this->request->expects($this->once())->method('getInput')->willReturn('foo');
        $this->checker->tokenIsValid($this->request, $this->session);
    }

    /**
     * Tests that a null CSRF token returns false
     */
    public function testNullCsrfTokenReturnsFalse()
    {
        $this->assertFalse($this->checker->tokenIsValid($this->request, $this->session));
    }

    /**
     * Tests that the token is marked as valid for read HTTP GET method
     */
    public function testTokenIsValidForReadHttpGetMethod()
    {
        $this->request->expects($this->any())->method('getMethod')->willReturn(RequestMethods::GET);
        $this->checker->tokenIsValid($this->request, $this->session);
        // Essentially just test that we got here
        $this->assertTrue(true);
    }

    /**
     * Tests that the token is marked as valid for read HTTP HEAD method
     */
    public function testTokenIsValidForReadHttpHeadMethod()
    {
        $this->request->expects($this->any())->method('getMethod')->willReturn(RequestMethods::HEAD);
        $this->checker->tokenIsValid($this->request, $this->session);
        // Essentially just test that we got here
        $this->assertTrue(true);
    }

    /**
     * Tests that the token is marked as valid for read HTTP OPTIONS method
     */
    public function testTokenIsValidForReadHttpOptionsMethod()
    {
        $this->request->expects($this->any())->method('getMethod')->willReturn(RequestMethods::OPTIONS);
        $this->checker->tokenIsValid($this->request, $this->session);
        // Essentially just test that we got here
        $this->assertTrue(true);
    }
}
