<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the augmenting query builder
 */
namespace RDev\QueryBuilders;

class AugmentingQueryBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests adding multiple columns
     */
    public function testAddingMultipleColumns()
    {
        $queryBuilder = new AugmentingQueryBuilder();
        $queryBuilder->addColumnValues(["name" => "dave"]);
        $queryBuilder->addColumnValues(["email" => "foo@bar.com"]);
        $this->assertEquals(["name" => "dave", "email" => "foo@bar.com"], $queryBuilder->getColumnNamesToValues());
    }

    /**
     * Tests adding a single column
     */
    public function testAddingSingleColumn()
    {
        $queryBuilder = new AugmentingQueryBuilder();
        $queryBuilder->addColumnValues(["name" => "dave"]);
        $this->assertEquals(["name" => "dave"], $queryBuilder->getColumnNamesToValues());
    }
} 