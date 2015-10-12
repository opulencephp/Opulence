<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the element registrant
 */
namespace Opulence\Console\Responses\Compilers;

use Opulence\Console\Responses\Compilers\Elements\Colors;
use Opulence\Console\Responses\Compilers\Elements\Style;
use Opulence\Console\Responses\Compilers\Elements\TextStyles;

class ElementRegistrant
{
    /**
     * Registers the Apex elements
     *
     * @param ICompiler $compiler The compiler to register to
     */
    public function registerElements(ICompiler &$compiler)
    {
        $compiler->registerElement("success", new Style(Colors::BLACK, Colors::GREEN));
        $compiler->registerElement("info", new Style(Colors::GREEN));
        $compiler->registerElement("error", new Style(Colors::BLACK, Colors::YELLOW));
        $compiler->registerElement("fatal", new Style(Colors::WHITE, Colors::RED));
        $compiler->registerElement("question", new Style(Colors::WHITE, Colors::BLUE));
        $compiler->registerElement("comment", new Style(Colors::YELLOW));
        $compiler->registerElement("b", new Style(null, null, [TextStyles::BOLD]));
        $compiler->registerElement("u", new Style(null, null, [TextStyles::UNDERLINE]));
    }
}