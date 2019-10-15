<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Validation\Tests\Rules\Errors;

use InvalidArgumentException;
use Opulence\Validation\Rules\Errors\ErrorTemplateRegistry;
use PHPUnit\Framework\TestCase;

/**
 * Tests the error template registry
 */
class ErrorTemplateRegistryTest extends TestCase
{
    private ErrorTemplateRegistry $registry;

    protected function setUp(): void
    {
        $this->registry = new ErrorTemplateRegistry();
    }

    public function testEmptyStringReturnedWhenNoTemplateExists(): void
    {
        $this->assertEquals('', $this->registry->getErrorTemplate('foo', 'bar'));
    }

    public function testExceptionThrownWithEmptyKeyInConfig(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->registry->registerErrorTemplatesFromConfig(['' => 'template']);
    }

    public function testExceptionThrownWithInvalidFieldTemplateKeyInConfig(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->registry->registerErrorTemplatesFromConfig([' . ' => 'template']);
    }

    public function testFieldTemplateOverridesGlobalTemplate(): void
    {
        $this->registry->registerFieldErrorTemplate('field', 'foo', 'field template');
        $this->registry->registerGlobalErrorTemplate('foo', 'global template');
        $this->assertEquals('field template', $this->registry->getErrorTemplate('field', 'foo'));
    }

    public function testFieldTemplateOverridesGlobalTemplateWhenRegisteringFromConfig(): void
    {
        $this->registry->registerErrorTemplatesFromConfig([
            'field.foo' => 'field template',
            'foo' => 'global template'
        ]);
        $this->assertEquals('field template', $this->registry->getErrorTemplate('field', 'foo'));
    }

    public function testOverwritingGlobalTemplate(): void
    {
        $this->registry->registerGlobalErrorTemplate('foo', 'template 1');
        $this->registry->registerGlobalErrorTemplate('foo', 'template 2');
        $this->assertEquals('template 2', $this->registry->getErrorTemplate('field', 'foo'));
    }

    public function testRegisteringFieldTemplate(): void
    {
        $this->registry->registerFieldErrorTemplate('field', 'foo', 'bar baz');
        $this->assertEquals('bar baz', $this->registry->getErrorTemplate('field', 'foo'));
    }

    public function testRegisteringFieldTemplateFromConfig(): void
    {
        $this->registry->registerErrorTemplatesFromConfig([
            'field.foo' => 'field template'
        ]);
        $this->assertEquals('field template', $this->registry->getErrorTemplate('field', 'foo'));
    }

    public function testRegisteringGlobalTemplate(): void
    {
        $this->registry->registerGlobalErrorTemplate('foo', 'bar baz');
        $this->assertEquals('bar baz', $this->registry->getErrorTemplate('field', 'foo'));
    }

    public function testRegisteringGlobalTemplateFromConfig(): void
    {
        $this->registry->registerErrorTemplatesFromConfig([
            'foo' => 'global template'
        ]);
        $this->assertEquals('global template', $this->registry->getErrorTemplate('field', 'foo'));
    }
}
