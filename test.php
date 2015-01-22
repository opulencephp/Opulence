<?php
use RDev\Console\Responses\Compilers\Compiler;
use RDev\Console\Responses\Formatters\Elements\Element;
use RDev\Console\Responses\Formatters\Elements\ElementRegistry;
use RDev\Console\Responses\Formatters\Elements\Style;

require_once __DIR__ . "/vendor/autoload.php";

$compiler = new Compiler();
$elementRegistry = new ElementRegistry();
$elementRegistry->registerElement(new Element("foo", new Style("green", "white")));
$elementRegistry->registerElement(new Element("bar", new Style("cyan")));
echo $compiler->compile("<foo>bar<bar>blah</bar></foo>", $elementRegistry);