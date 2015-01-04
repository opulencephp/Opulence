<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the YAML config reader
 */
namespace RDev\Configs\Readers;

class YAMLReaderTest extends \PHPUnit_Framework_TestCase
{
    /** The path to the valid YAML config */
    const VALID_YAML_CONFIG_PATH = "/files/validYAMLConfig.yml";
    /** The path to the invalid YAML config */
    const INVALID_YAML_CONFIG_PATH = "/files/invalidYAMLConfig.yml";
    /** The path to a file with an invalid extension */
    const INVALID_EXTENSION_CONFIG_PATH = "/files/config.txt";

    /** @var YAMLReader The reader to use in the tests */
    private $yamlReader = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->yamlReader = new YAMLReader();
    }

    /**
     * Tests passing in an invalid class name when reading from a file
     */
    public function testPassingInInvalidConfigClassNameWhenReadingFromFile()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->yamlReader->readFromFile(__DIR__ . self::VALID_YAML_CONFIG_PATH, get_class($this));
    }

    /**
     * Tests passing in an invalid class name when reading from input
     */
    public function testPassingInInvalidConfigClassNameWhenReadingFromInput()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $yaml = 'foo: bar';
        $this->yamlReader->readFromInput($yaml, get_class($this));
    }

    /**
     * Tests reading from input that is of the wrong type
     */
    public function testReadingFromInputWithInvalidParameterType()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->yamlReader->readFromInput(["Not valid YAML string"]);
    }

    /**
     * Tests reading from an invalid file
     */
    public function testReadingFromInvalidFile()
    {
        $this->setExpectedException("\\RuntimeException");
        $this->yamlReader->readFromFile(__DIR__ . self::INVALID_YAML_CONFIG_PATH);
    }

    /**
     * Tests reading from invalid input
     */
    public function testReadingFromInvalidInput()
    {
        $this->setExpectedException("\\RuntimeException");
        $this->yamlReader->readFromInput('[');
    }

    /**
     * Tests reading from a valid file
     */
    public function testReadingFromValidFile()
    {
        $config = $this->yamlReader->readFromFile(__DIR__ . self::VALID_YAML_CONFIG_PATH);
        $this->assertEquals(["foo" => "bar"], $config->getArrayCopy());
    }

    /**
     * Tests reading from valid input
     */
    public function testReadingFromValidInput()
    {
        $yaml = 'foo: bar';
        $config = $this->yamlReader->readFromInput($yaml);
        $this->assertEquals(["foo" => "bar"], $config->getArrayCopy());
    }

    /**
     * Tests using a config file with an invalid extension
     */
    public function testUsingConfigFileWithInvalidExtension()
    {
        $this->setExpectedException("\\RuntimeException");
        $this->yamlReader->readFromFile(__DIR__ . self::INVALID_EXTENSION_CONFIG_PATH);
    }
} 