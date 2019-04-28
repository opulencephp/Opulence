<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\QueryBuilders\Tests;

use Opulence\QueryBuilders\InvalidQueryException;
use Opulence\QueryBuilders\Query;
use PDO;

/**
 * Tests the query class
 */
class QueryTest extends \PHPUnit\Framework\TestCase
{
    /** @var Query The query object stub */
    private $query;

    /**
     * Sets up the test
     */
    protected function setUp(): void
    {
        $this->query = $this->getMockForAbstractClass(Query::class);
    }

    /**
     * Tests adding a named placeholder
     */
    public function testAddingNamedPlaceholder(): void
    {
        $this->query->addNamedPlaceholderValue('name', 'foo');
        $this->assertEquals([
            'name' => ['foo', PDO::PARAM_STR]
        ], $this->query->getParameters());
    }

    /**
     * Tests the exception that should be thrown when adding a named placeholder after an unnamed one
     */
    public function testAddingNamedPlaceholderAfterAddingUnnamedPlaceholder(): void
    {
        $this->expectException(InvalidQueryException::class);
        $this->query->addUnnamedPlaceholderValue('dave')
            ->addNamedPlaceholderValue('id', 18175);
    }

    /**
     * Tests adding a named placeholder with data type
     */
    public function testAddingNamedPlaceholderWithDataType(): void
    {
        $this->query->addNamedPlaceholderValue('userId', 18175, PDO::PARAM_INT);
        $this->assertEquals([
            'userId' => [18175, PDO::PARAM_INT]
        ], $this->query->getParameters());
    }

    /**
     * Tests adding an array with the named value with the incorrect number of arguments
     */
    public function testAddingNamedPlaceholderWithIncorrectArrayValueCount(): void
    {
        $this->expectException(InvalidQueryException::class);
        $this->query->addNamedPlaceholderValues(['foo' => ['bar']]);
    }

    /**
     * Tests adding an unnamed placeholder
     */
    public function testAddingUnnamedPlaceholder(): void
    {
        $this->query->addUnnamedPlaceholderValue('foo');
        $this->assertEquals([
            ['foo', PDO::PARAM_STR]
        ], $this->query->getParameters());
    }

    /**
     * Tests the exception that should be thrown when adding an unnamed placeholder after a named one
     */
    public function testAddingUnnamedPlaceholderAfterAddingNamedPlaceholder(): void
    {
        $this->expectException(InvalidQueryException::class);
        $this->query->addNamedPlaceholderValue('id', 18175)
            ->addUnnamedPlaceholderValue('dave');
    }

    /**
     * Tests adding an unnamed placeholder with data type
     */
    public function testAddingUnnamedPlaceholderWithDataType(): void
    {
        $this->query->addUnnamedPlaceholderValue(18175, PDO::PARAM_INT);
        $this->assertEquals([
            [18175, PDO::PARAM_INT]
        ], $this->query->getParameters());
    }

    /**
     * Tests adding an array with the unnamed value with the incorrect number of arguments
     */
    public function testAddingUnnamedPlaceholderWithIncorrectArrayValueCount(): void
    {
        $this->expectException(InvalidQueryException::class);
        $this->query->addUnnamedPlaceholderValues([['bar']]);
    }

    /**
     * Tests removing a named placeholder
     */
    public function testRemovingNamedPlaceholder(): void
    {
        $key = 'foo';
        $this->query = $this->getMockForAbstractClass(Query::class);
        $this->query->addNamedPlaceholderValue($key, 'bar');
        $this->query->removeNamedPlaceHolder($key);
        $this->assertFalse(array_key_exists($key, $this->query->getParameters()));
    }

    /**
     * Tests removing a named placeholder when using unnamed placeholders
     */
    public function testRemovingNamedPlaceholderWhenUsingUnnamedPlaceholders(): void
    {
        $this->expectException(InvalidQueryException::class);
        $this->query->addUnnamedPlaceholderValue('foo');
        $this->query->removeNamedPlaceHolder('bar');
    }

    /**
     * Tests removing an unnamed placeholder
     */
    public function testRemovingUnnamedPlaceholder(): void
    {
        $this->query = $this->getMockForAbstractClass(Query::class);
        $this->query->addUnnamedPlaceholderValue('foo')
            ->addUnnamedPlaceholderValue('bar')
            ->addUnnamedPlaceholderValue('xyz');
        $this->query->removeUnnamedPlaceHolder(1);
        $parameters = $this->query->getParameters();
        $fooFound = false;

        foreach ($parameters as $parameterData) {
            if ($parameterData[0] === 'bar') {
                $fooFound = true;

                break;
            }
        }

        $this->assertFalse($fooFound);
    }

    /**
     * Tests removing an unnamed placeholder when using named placeholders
     */
    public function testRemovingUnnamedPlaceholderWhenUsingNamedPlaceholders(): void
    {
        $this->expectException(InvalidQueryException::class);
        $this->query->addNamedPlaceholderValue('foo', 'bar');
        $this->query->removeUnnamedPlaceHolder(0);
    }
}
