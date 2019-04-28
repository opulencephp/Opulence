<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Console\Prompts\Questions;

/**
 * Defines a confirmation question
 */
class Confirmation extends Question
{
    /**
     * @param string $question The question text
     * @param bool $defaultAnswer The default answer to the question
     */
    public function __construct(string $question, bool $defaultAnswer = true)
    {
        parent::__construct($question, $defaultAnswer);
    }

    /**
     * @inheritdoc
     */
    public function formatAnswer($answer): bool
    {
        if (is_bool($answer)) {
            return $answer;
        }

        // Accept anything that begins with "y" like "y", "yes", and "YES"
        return mb_strtolower($answer[0]) === 'y';
    }
}
