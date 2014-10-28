<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the JSON config reader
 */
namespace RDev\Configs\Readers;

class JSONReaderTest extends \PHPUnit_Framework_TestCase
{
    /** The path to the valid JSON config */
    const VALID_JSON_CONFIG_PATH = "/files/validJSONConfig.json";
    /** The path to the invalid JSON config */
    const INVALID_JSON_CONFIG_PATH = "/files/invalidJSONConfig.json";
    /** The path to a file with an invalid extension */
    const INVALID_EXTENSION_CONFIG_PATH = "/files/config.txt";

    /** @var JSONReader The reader to use in the tests */
    private $jsonReader = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->jsonReader = new JSONReader();
    }

    /**
     * Tests passing in an invalid class name when reading from a file
     */
    public function testPassingInInvalidConfigClassNameWhenReadingFromFile()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->jsonReader->readFromFile(__DIR__ . self::VALID_JSON_CONFIG_PATH, get_class($this));
    }

    /**
     * Tests passing in an invalid class name when reading from input
     */
    public function testPassingInInvalidConfigClassNameWhenReadingFromInput()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $json = '{"foo":"bar"}';
        $this->jsonReader->readFromInput($json, get_class($this));
    }

    /**
     * Tests reading from input that is of the wrong type
     */
    public function testReadingFromInputWithInvalidParameterType()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->jsonReader->readFromInput(["Not valid JSON string"]);
    }

    /**
     * Tests reading from an invalid file
     */
    public function testReadingFromInvalidFile()
    {
        $this->setExpectedException("\\RuntimeException");
        $this->jsonReader->readFromFile(__DIR__ . self::INVALID_JSON_CONFIG_PATH);
    }

    /**
     * Tests reading from invalid input
     */
    public function testReadingFromInvalidInput()
    {
        $this->setExpectedException("\\RuntimeException");
        $this->jsonReader->readFromInput('{Not Valid JSON');
    }

    /**
     * Tests reading from a valid file
     */
    public function testReadingFromValidFile()
    {
        $config = $this->jsonReader->readFromFile(__DIR__ . self::VALID_JSON_CONFIG_PATH);
        $this->assertEquals(["foo" => "bar"], $config->getArrayCopy());
    }

    /**
     * Tests reading from valid input
     */
    public function testReadingFromValidInput()
    {
        $json = '{"foo":"bar"}';
        $config = $this->jsonReader->readFromInput($json);
        $this->assertEquals(["foo" => "bar"], $config->getArrayCopy());
    }

    /**
     * Tests using a config file with an invalid extension
     */
    public function testUsingConfigFileWithInvalidExtension()
    {
        $this->setExpectedException("\\RuntimeException");
        $this->jsonReader->readFromFile(__DIR__ . self::INVALID_EXTENSION_CONFIG_PATH);
    }
} 