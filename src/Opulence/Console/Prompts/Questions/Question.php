<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Console\Prompts\Questions;

/**
 * Defines a console prompt question
 */
class Question implements IQuestion
{
    /** @var string The question text */
    private $question = '';
    /** @var mixed The default answer to the question */
    private $defaultAnswer = null;

    /**
     * @param string $question The question text
     * @param mixed $defaultAnswer The default answer to the question
     */
    public function __construct(string $question, $defaultAnswer = null)
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
    public function getText() : string
    {
        return $this->question;
    }
}
