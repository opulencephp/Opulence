<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\QueryBuilders\tests;

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

    protected function setUp(): void
    {
        $this->query = $this->getMockForAbstractClass(Query::class);
    }

    public function testAddingNamedPlaceholder(): void
    {
        $this->query->addNamedPlaceholderValue('name', 'foo');
        $this->assertEquals([
            'name' => ['foo', PDO::PARAM_STR]
        ], $this->query->getParameters());
    }

    public function testAddingNamedPlaceholderAfterAddingUnnamedPlaceholder(): void
    {
        $this->expectException(InvalidQueryException::class);
        $this->query->addUnnamedPlaceholderValue('dave')
            ->addNamedPlaceholderValue('id', 18175);
    }

    public function testAddingNamedPlaceholderWithDataType(): void
    {
        $this->query->addNamedPlaceholderValue('userId', 18175, PDO::PARAM_INT);
        $this->assertEquals([
            'userId' => [18175, PDO::PARAM_INT]
        ], $this->query->getParameters());
    }

    public function testAddingNamedPlaceholderWithIncorrectArrayValueCount(): void
    {
        $this->expectException(InvalidQueryException::class);
        $this->query->addNamedPlaceholderValues(['foo' => ['bar']]);
    }

    public function testAddingUnnamedPlaceholder(): void
    {
        $this->query->addUnnamedPlaceholderValue('foo');
        $this->assertEquals([
            ['foo', PDO::PARAM_STR]
        ], $this->query->getParameters());
    }

    public function testAddingUnnamedPlaceholderAfterAddingNamedPlaceholder(): void
    {
        $this->expectException(InvalidQueryException::class);
        $this->query->addNamedPlaceholderValue('id', 18175)
            ->addUnnamedPlaceholderValue('dave');
    }

    public function testAddingUnnamedPlaceholderWithDataType(): void
    {
        $this->query->addUnnamedPlaceholderValue(18175, PDO::PARAM_INT);
        $this->assertEquals([
            [18175, PDO::PARAM_INT]
        ], $this->query->getParameters());
    }

    public function testAddingUnnamedPlaceholderWithIncorrectArrayValueCount(): void
    {
        $this->expectException(InvalidQueryException::class);
        $this->query->addUnnamedPlaceholderValues([['bar']]);
    }

    public function testRemovingNamedPlaceholder(): void
    {
        $key = 'foo';
        $this->query = $this->getMockForAbstractClass(Query::class);
        $this->query->addNamedPlaceholderValue($key, 'bar');
        $this->query->removeNamedPlaceHolder($key);
        $this->assertFalse(array_key_exists($key, $this->query->getParameters()));
    }

    public function testRemovingNamedPlaceholderWhenUsingUnnamedPlaceholders(): void
    {
        $this->expectException(InvalidQueryException::class);
        $this->query->addUnnamedPlaceholderValue('foo');
        $this->query->removeNamedPlaceHolder('bar');
    }

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

    public function testRemovingUnnamedPlaceholderWhenUsingNamedPlaceholders(): void
    {
        $this->expectException(InvalidQueryException::class);
        $this->query->addNamedPlaceholderValue('foo', 'bar');
        $this->query->removeUnnamedPlaceHolder(0);
    }
}
