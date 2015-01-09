<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the interface for questions to implement
 */
namespace RDev\Console\Prompts\Questions;

interface IQuestion
{
    /**
     * Formats an answer
     * Useful for subclasses to override
     *
     * @param mixed $answer The answer to format
     * @return mixed The formatted answer
     * @throws \InvalidArgumentException Thrown if the answer is not of the correct type
     */
    public function formatAnswer($answer);

    /**
     * Gets the default response
     *
     * @return mixed The default response
     */
    public function getDefaultResponse();

    /**
     * Gets the question text
     *
     * @return string The question text
     */
    public function getText();
}