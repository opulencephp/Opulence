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

use Opulence\Console\Prompts\Questions\Confirmation;

/**
 * Tests the confirmation question
 */
class ConfirmationTest extends \PHPUnit\Framework\TestCase
{
    /** @var Confirmation The question to use in tests */
    private $question;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->question = new Confirmation('Is Dave cool (yn)');
    }

    /**
     * Tests formatting false values
     */
    public function testFormattingFalseValues(): void
    {
        $this->assertFalse($this->question->formatAnswer('n'));
        $this->assertFalse($this->question->formatAnswer('N'));
        $this->assertFalse($this->question->formatAnswer('no'));
        $this->assertFalse($this->question->formatAnswer('NO'));
    }

    /**
     * Tests formatting true values
     */
    public function testFormattingTrueValues(): void
    {
        $this->assertTrue($this->question->formatAnswer('y'));
        $this->assertTrue($this->question->formatAnswer('Y'));
        $this->assertTrue($this->question->formatAnswer('yes'));
        $this->assertTrue($this->question->formatAnswer('YES'));
    }
}
