<?php
use Monolog;
use RDev\HTTP\Kernels;
use RDev\HTTP\Requests;
use RDev\HTTP\Routing;
use RDev\HTTP\Routing\Compilers;
use RDev\HTTP\Routing\Compilers\Parsers;
use RDev\HTTP\Routing\Dispatchers;
use RDev\IoC;

require __DIR__ . "/vendor/autoload.php";

$container = new IoC\Container();
$routeCompiler = new Compilers\Compiler(new Parsers\Parser());
$router = new Routing\Router(new Dispatchers\Dispatcher($container), $routeCompiler);
$logger = new Monolog\Logger("kernelTest");
$request = Requests\Request::createFromGlobals();
$kernel = new Kernels\Kernel($container, $router, $logger);
$kernel->addMiddleware("RDev\\Tests\\HTTP\\Middleware\\Mocks\\HeaderSetter");
$request = Requests\Request::createFromGlobals();
$response = $kernel->handle($request);
$response->getHeaders()->get("foo");
$response->send();