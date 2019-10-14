<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Validation\Tests\Rules\Factories;

use Opulence\Validation\Rules\Errors\Compilers\ICompiler;
use Opulence\Validation\Rules\Errors\ErrorTemplateRegistry;
use Opulence\Validation\Rules\Factories\RulesFactory;
use Opulence\Validation\Rules\RuleExtensionRegistry;
use Opulence\Validation\Rules\Rules;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the rules factory
 */
class RulesFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testRulesCreated(): void
    {
        /** @var RuleExtensionRegistry|MockObject $ruleExtensionRegistry */
        $ruleExtensionRegistry = $this->createMock(RuleExtensionRegistry::class);
        /** @var ErrorTemplateRegistry|MockObject $errorTemplateRegistry */
        $errorTemplateRegistry = $this->createMock(ErrorTemplateRegistry::class);
        /** @var ICompiler|MockObject $errorTemplateCompiler */
        $errorTemplateCompiler = $this->createMock(ICompiler::class);
        $factory = new RulesFactory($ruleExtensionRegistry, $errorTemplateRegistry, $errorTemplateCompiler);
        $this->assertInstanceOf(Rules::class, $factory->createRules());
    }
}
