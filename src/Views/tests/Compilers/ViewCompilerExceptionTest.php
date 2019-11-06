<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Views\Tests\Compilers;

use Opulence\Views\Compilers\ViewCompilerException;
use Opulence\Views\IView;
use PHPUnit\Framework\TestCase;

/**
 * Tests the view compiler exception
 */
class ViewCompilerExceptionTest extends TestCase
{
    public function testExceptionMessageIncludesViewPath(): void
    {
        $view = $this->createMock(IView::class);
        $view->expects($this->once())
            ->method('getPath')
            ->willReturn('foo.php');
        $this->assertEquals('Exception thrown by view foo.php', (new ViewCompilerException($view))->getMessage());
    }
}
