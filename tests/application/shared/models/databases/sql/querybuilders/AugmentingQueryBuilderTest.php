<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the augmenting query builder
 */
namespace RDev\Application\Shared\Models\Databases\SQL\QueryBuilders;

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