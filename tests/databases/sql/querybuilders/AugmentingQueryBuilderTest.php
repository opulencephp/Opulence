<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the augmenting query builder
 */
namespace RamODev\Databases\SQL\QueryBuilders;

require_once(__DIR__ . "/../../../../databases/sql/querybuilders/AugmentingQueryBuilder.php");

class AugmentingQueryBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests adding multiple columns
     */
    public function testAddingMultipleColumns()
    {
        $queryBuilder = new AugmentingQueryBuilder();
        $queryBuilder->addColumnValues(array("name" => "dave"));
        $queryBuilder->addColumnValues(array("email" => "foo@bar.com"));
        $this->assertEquals(array("name" => "dave", "email" => "foo@bar.com"), $queryBuilder->getColumnNamesToValues());
    }

    /**
     * Tests adding a single column
     */
    public function testAddingSingleColumn()
    {
        $queryBuilder = new AugmentingQueryBuilder();
        $queryBuilder->addColumnValues(array("name" => "dave"));
        $this->assertEquals(array("name" => "dave"), $queryBuilder->getColumnNamesToValues());
    }
} 