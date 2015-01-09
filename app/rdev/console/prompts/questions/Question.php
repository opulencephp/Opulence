<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines a console prompt question
 */
namespace RDev\Console\Prompts\Questions;

class Question implements IQuestion
{
    /** @var string The question text */
    private $question = "";
    /** @var mixed The default value for the response */
    private $defaultResponse = null;

    /**
     * @param string $question The question text
     * @param mixed $defaultResponse The default value for the response
     */
    public function __construct($question, $defaultResponse = null)
    {
        $this->question = $question;
        $this->defaultResponse = $defaultResponse;
    }

    /**
     * {@inheritdoc}
     */
    public function formatAnswer($answer)
    {
        // By default, just return the answer
        return $answer;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultResponse()
    {
        return $this->defaultResponse;
    }

    /**
     * {@inheritdoc}
     */
    public function getText()
    {
        return $this->question;
    }
}