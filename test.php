<?php
namespace RDev\Views\Compilers\SubCompilers;
use RDev\Files;
use RDev\Views\Compilers;
use RDev\HTTP\Requests;
use RDev\Tests\Mocks;
use RDev\Tests\Views\Mocks as ViewMocks;
use RDev\Tests\Views\Compilers\Mocks as CompilerMocks;
use RDev\Tests\Views\Compilers\SubCompilers\Mocks as SubCompilerMocks;
use RDev\Views;
use RDev\Views\Cache;
use RDev\Views\Factories;
use RDev\Views\Filters;

require_once "vendor/autoload.php";

$xssFilter = new Filters\XSS();
$fileSystem = new Files\FileSystem();
$cache = new Cache\Cache($fileSystem, "/tmp");
$templateFactory = new Factories\TemplateFactory($fileSystem, __DIR__ . "/tests/app/rdev/views/files");
$compiler = new Compilers\Compiler($cache, $templateFactory, $xssFilter);
$template = new Views\Template();
$subCompiler = new TagCompiler($compiler, $xssFilter);

$template->setContents("{{!content!}}");
$template->setTag("message", "world");
$template->setTag("content", "Hello, {{!message!}}!");
$subCompiler->compile($template, $template->getContents());