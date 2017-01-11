<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Http\Responses;

/**
 * Tests the redirect response class
 */
class RedirectResponseTest extends \PHPUnit\Framework\TestCase
{
    /** @var RedirectResponse The response to use in tests */
    private $redirectResponse = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->redirectResponse = new RedirectResponse('/foo', ResponseHeaders::HTTP_ACCEPTED, ['HTTP_FOO' => 'bar']);
    }

    /**
     * Tests getting the headers after setting them in the constructor
     */
    public function testGettingHeadersAfterSettingInConstructor()
    {
        $this->assertEquals([
            'foo' => ['bar'],
            'location' => ['/foo']
        ], $this->redirectResponse->getHeaders()->getAll());
    }

    /**
     * Tests getting the status code after setting it in the constructor
     */
    public function testGettingStatusCodeAfterSettingInConstructor()
    {
        $this->assertEquals(ResponseHeaders::HTTP_ACCEPTED, $this->redirectResponse->getStatusCode());
    }

    /**
     * Tests getting the target URL after setting it in the constructor
     */
    public function testGettingTargetUrlAfterSettingInConstructor()
    {
        $this->assertEquals('/foo', $this->redirectResponse->getTargetUrl());
        $this->assertEquals('/foo', $this->redirectResponse->getHeaders()->get('Location'));
    }

    /**
     * Tests setting the target URL
     */
    public function testSettingTargetUrl()
    {
        $this->redirectResponse->setTargetUrl('/bar');
        $this->assertEquals('/bar', $this->redirectResponse->getTargetUrl());
        $this->assertEquals('/bar', $this->redirectResponse->getHeaders()->get('Location'));
    }
}
