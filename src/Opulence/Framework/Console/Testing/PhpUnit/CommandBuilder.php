<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Framework\Console\Testing\PhpUnit;

/**
 * Defines the command builder for console applications
 */
class CommandBuilder
{
    /** @var IntegrationTestCase The integration test that created this */
    protected $integrationTest = null;
    /** @var string The command name */
    protected $commandName = null;
    /** @var array The list of arguments */
    protected $arguments = [];
    /** @var array The list of options */
    protected $options = [];
    /** @var array The list of prompt answers */
    protected $promptAnswers = [];
    /** @var bool Whether or not the response is styled */
    protected $isStyled = true;

    /**
     * @param IntegrationTestCase $integrationTest The integration test that created this builder
     * @param string $commandName The command name
     */
    public function __construct(IntegrationTestCase $integrationTest, string $commandName)
    {
        $this->integrationTest = $integrationTest;
        $this->commandName = $commandName;
    }

    /**
     * Executes the built command
     *
     * @return IntegrationTestCase For method chaining
     */
    public function execute() : IntegrationTestCase
    {
        return $this->integrationTest->execute(
            $this->commandName,
            $this->arguments,
            $this->options,
            $this->promptAnswers,
            $this->isStyled
        );
    }

    /**
     * Adds prompt answers to the command
     *
     * @param array|string $answers The answers to add
     * @param bool $overwriteOld Whether or not to overwrite all old answers
     * @return self For method chaining
     */
    public function withAnswers($answers, bool $overwriteOld = false) : self
    {
        $answers = (array)$answers;
        $this->addValuesToCollection($answers, $this->promptAnswers, $overwriteOld);

        return $this;
    }

    /**
     * Adds arguments to the command
     *
     * @param array|string $arguments The arguments to add
     * @param bool $overwriteOld Whether or not to overwrite all old arguments
     * @return self For method chaining
     */
    public function withArguments($arguments, bool $overwriteOld = false) : self
    {
        $arguments = (array)$arguments;
        $this->addValuesToCollection($arguments, $this->arguments, $overwriteOld);

        return $this;
    }

    /**
     * Adds options to the command
     *
     * @param array|string $options The options to add
     * @param bool $overwriteOld Whether or not to overwrite all old options
     * @return self For method chaining
     */
    public function withOptions($options, bool $overwriteOld = false) : self
    {
        $options = (array)$options;
        $this->addValuesToCollection($options, $this->options, $overwriteOld);

        return $this;
    }

    /**
     * Sets whether or not the response is styled
     *
     * @param bool $isStyled Whether or not the response is styled
     * @return self For method chaining
     */
    public function withStyle(bool $isStyled) : self
    {
        $this->isStyled = $isStyled;

        return $this;
    }

    /**
     * Adds values to a collection
     *
     * @param array $values The values to add
     * @param array $collection The collection to add to
     * @param bool $overwriteOld Whether or not clear the collection before adding the new values
     */
    private function addValuesToCollection(array $values, array &$collection, bool $overwriteOld)
    {
        if ($overwriteOld) {
            $collection = [];
        }

        $collection = array_merge($collection, $values);
    }
}
