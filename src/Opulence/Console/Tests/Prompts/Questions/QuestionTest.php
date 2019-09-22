<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Console\Tests\Prompts\Questions;

use Opulence\Console\Prompts\Questions\Question;

/**
 * Tests the console prompt question
 */
class QuestionTest extends \PHPUnit\Framework\TestCase
{
    /** @var Question The question to use in tests */
    private $question;

    protected function setUp(): void
    {
        $this->question = new Question('Dummy question', 'foo');
    }

    public function testFormattingAnswer(): void
    {
        $this->assertEquals('foo', $this->question->formatAnswer('foo'));
    }

    public function testGettingDefaultResponse(): void
    {
        $this->assertEquals('foo', $this->question->getDefaultAnswer());
    }

    public function testGettingQuestion(): void
    {
        $this->assertEquals('Dummy question', $this->question->getText());
    }
}
