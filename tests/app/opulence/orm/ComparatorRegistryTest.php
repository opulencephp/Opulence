<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the comparator registry
 */
namespace Opulence\ORM;

class ComparatorRegistryTest extends \PHPUnit_Framework_TestCase
{
    /** @var ComparatorRegistry The registry to use in tests */
    private $registry = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->registry = new ComparatorRegistry();
    }
}