<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Console\Tests\Requests;

use InvalidArgumentException;
use Opulence\Console\Requests\Request;

/**
 * Tests the console request
 */
class RequestTest extends \PHPUnit\Framework\TestCase
{
    /** @var Request The request to use in tests */
    private $request;

    protected function setUp(): void
    {
        $this->request = new Request();
    }

    public function testAddingMultipleValuesForOption(): void
    {
        $this->request->addOptionValue('foo', 'bar');
        $this->assertEquals('bar', $this->request->getOptionValue('foo'));
        $this->request->addOptionValue('foo', 'baz');
        $this->assertEquals(['bar', 'baz'], $this->request->getOptionValue('foo'));
    }

    public function testCheckingIfOptionWithValueIsSet(): void
    {
        $this->request->addOptionValue('foo', 'bar');
        $this->assertTrue($this->request->optionIsSet('foo'));
    }

    public function testCheckingIfOptionWithoutValueIsSet(): void
    {
        $this->request->addOptionValue('foo', null);
        $this->assertTrue($this->request->optionIsSet('foo'));
    }

    public function testGettingAllArguments(): void
    {
        $this->request->addArgumentValue('foo');
        $this->request->addArgumentValue('bar');
        $this->assertEquals(['foo', 'bar'], $this->request->getArgumentValues());
    }

    public function testGettingAllOptions(): void
    {
        $this->request->addOptionValue('foo', 'bar');
        $this->request->addOptionValue('baz', 'blah');
        $this->assertEquals(['foo' => 'bar', 'baz' => 'blah'], $this->request->getOptionValues());
    }

    public function testGettingCommandName(): void
    {
        $this->request->setCommandName('foo');
        $this->assertEquals('foo', $this->request->getCommandName());
    }

    /**
     * Tests getting a non-existent option
     */
    public function testGettingNonExistentOption(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->request->getOptionValue('foo');
    }

    public function testGettingOption(): void
    {
        $this->request->addOptionValue('foo', 'bar');
        $this->assertEquals('bar', $this->request->getOptionValue('foo'));
    }
}
