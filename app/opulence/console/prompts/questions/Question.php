<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a console prompt question
 */
namespace Opulence\Console\Prompts\Questions;

class Question implements IQuestion
{
    /** @var string The question text */
    private $question = "";
    /** @var mixed The default answer to the question */
    private $defaultAnswer = null;

    /**
     * @param string $question The question text
     * @param mixed $defaultAnswer The default answer to the question
     */
    public function __construct($question, $defaultAnswer = null)
    {
        $this->question = $question;
        $this->defaultAnswer = $defaultAnswer;
    }

    /**
     * @inheritdoc
     */
    public function formatAnswer($answer)
    {
        // By default, just return the answer
        return $answer;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultAnswer()
    {
        return $this->defaultAnswer;
    }

    /**
     * @inheritdoc
     */
    public function getText()
    {
        return $this->question;
    }
}