<?php
use RDev\Console\Responses\Compilers\Compiler;
use RDev\Console\Responses\Compilers\Lexers\Lexer;
use RDev\Console\Responses\Compilers\Parsers\Parser;
use RDev\Console\Responses\Compilers\Tokens;
use RDev\Console\Responses\Formatters\Elements\Element;
use RDev\Console\Responses\Formatters\Elements\Elements;
use RDev\Console\Responses\Formatters\Elements\Style;

require_once __DIR__ . "/vendor/autoload.php";

$parser = new Parser();
$lexer = new Lexer();
$compiler = new Compiler($lexer, $parser);
$elements = new Elements();
$elements->add(new Element("y", new Style("green", "white")));
$elements->add(new Element("z", new Style("cyan")));
echo $compiler->compile("<y>a<z>b</z>c</y>", $elements);
/*$tokens =  [
    new Tokens\Token(Tokens\TokenTypes::T_TAG_OPEN, "foo"),
    new Tokens\Token(Tokens\TokenTypes::T_WORD, "baz"),
    new Tokens\Token(Tokens\TokenTypes::T_EOF, null)
];
echo var_export($parser->parse($tokens), true);*/
/*
echo $compiler->compile("<foo>bar<bar>blah</bar></foo>", $elements);*/