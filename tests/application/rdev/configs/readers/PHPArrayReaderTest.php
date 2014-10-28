<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the PHP array config reader
 */
namespace RDev\Configs\Readers;

class PHPArrayReaderTest extends \PHPUnit_Framework_TestCase
{
    /** The path to the valid PHP array config */
    const VALID_PHP_ARRAY_CONFIG_PATH = "/files/validPHPArrayConfig.php";
    /** The path to the invalid PHP array config */
    const INVALID_PHP_ARRAY_CONFIG_PATH = "/files/invalidPHPArrayConfig.php";
    /** The path to a file with an invalid extension */
    const INVALID_EXTENSION_CONFIG_PATH = "/files/config.txt";

    /** @var PHPArrayReader The reader to use in the tests */
    private $phpArrayReader = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->phpArrayReader = new PHPArrayReader();
    }

    /**
     * Tests passing in an invalid class name when reading from a file
     */
    public function testPassingInInvalidConfigClassNameWhenReadingFromFile()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->phpArrayReader->readFromFile(__DIR__ . self::VALID_PHP_ARRAY_CONFIG_PATH, get_class($this));
    }

    /**
     * Tests passing in an invalid class name when reading from input
     */
    public function testPassingInInvalidConfigClassNameWhenReadingFromInput()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $array = ["foo" => "bar"];
        $this->phpArrayReader->readFromInput($array, get_class($this));
    }

    /**
     * Tests reading from input that is of the wrong type
     */
    public function testReadingFromInputWithInvalidParameterType()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->phpArrayReader->readFromInput('Not valid PHP array');
    }

    /**
     * Tests reading from an invalid file
     */
    public function testReadingFromInvalidFile()
    {
        $this->setExpectedException("\\RuntimeException");
        $this->phpArrayReader->readFromFile(__DIR__ . self::INVALID_PHP_ARRAY_CONFIG_PATH);
    }

    /**
     * Tests reading from a valid file
     */
    public function testReadingFromValidFile()
    {
        $config = $this->phpArrayReader->readFromFile(__DIR__ . self::VALID_PHP_ARRAY_CONFIG_PATH);
        $this->assertEquals(["foo" => "bar"], $config->getArrayCopy());
    }

    /**
     * Tests reading from valid input
     */
    public function testReadingFromValidInput()
    {
        $array = ["foo" => "bar"];
        $config = $this->phpArrayReader->readFromInput($array);
        $this->assertEquals($array, $config->getArrayCopy());
    }

    /**
     * Tests using a config file with an invalid extension
     */
    public function testUsingConfigFileWithInvalidExtension()
    {
        $this->setExpectedException("\\RuntimeException");
        $this->phpArrayReader->readFromFile(__DIR__ . self::INVALID_EXTENSION_CONFIG_PATH);
    }
} 