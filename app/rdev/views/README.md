# Templates

## Table of Contents
1. [Introduction](#introduction)
2. [Basic Usage](#basic-usage)
3. [Caching](#caching)
  1. [Garbage Collection](#garbage-collection)
4. [Cross-Site Scripting](#cross-site-scripting)
5. [Nesting Templates](#nesting-templates)
6. [Extending Templates](#extending-templates)
  1. [Example](#example)
  2. [Parts](#parts)
7. [Using PHP in Your Template](#using-php-in-your-template)
8. [Built-In Functions](#built-in-functions)
  1. [PHP Functions](#php-functions)
  2. [RDev Functions](#rdev-functions)
  3. [Using Template Functions in PHP Code](#using-template-functions-in-php-code)
9. [Custom Template Functions](#custom-template-functions)
10. [Extending the Compiler](#extending-the-compiler)
11. [Escaping Tags](#escaping-tags)
12. [Custom Tags](#custom-tags)
13. [Template Factory](#template-factory)
  1. [Builders](#builders)
  2. [Aliasing](#aliasing)

## Introduction
**RDev** has a template system, which is meant to simplify adding dynamic content to web pages.  You can inject data into your pages, create loops for generating iterative items, escape unsanitized text, and add your own tag extensions.  Unlike other popular template libraries out there, you can use plain old PHP for simple constructs such as if/else statements and loops.

## Basic Usage
Templates hold raw content for pages and page parts.  In order to compile this raw content into finished templates, we `compile()` them using a compiler that implements `RDev\Views\Compilers\ICompiler` (`RDev\Views\Compilers\Compiler` come built-in to RDev).  By separating compiling into a separate class, we separate the concerns of templates and compiling templates, thus satisfying the *Single Responsibility Principle* (*SRP*).  Let's take a look at a basic example:

##### Template
```
Hello, {{username}}
```
##### Application Code
```php
use RDev\Files;
use RDev\Views;
use RDev\Views\Cache;
use RDev\Views\Compilers;
use RDev\Views\Factories;
use RDev\Views\Filters;

$fileSystem = new Files\FileSystem();
$cache = new Cache\Cache($fileSystem, "/tmp");
$templateFactory = new Factories\TemplateFactory($fileSystem, PATH_TO_TEMPLATES);
$xssFilter = new Filters\XSS();
$compiler = new Compilers\Compiler($cache, $templateFactory, $xssFilter);
$template = new Views\Template();
$template->setContents($fileSystem->read(PATH_TO_HTML_TEMPLATE));
$template->setTag("username", "Dave");
echo $compiler->compile($template); // "Hello, Dave"
```

Alternatively, you could just pass in a template's contents to its constructor:
```php
use RDev\Files;
use RDev\Views;
use RDev\Views\Cache;
use RDev\Views\Compilers;
use RDev\Views\Factories;
use RDev\Views\Filters;

$cache = new Cache\Cache(new Files\FileSystem(), "/tmp");
$templateFactory = new Factories\TemplateFactory($fileSystem, PATH_TO_TEMPLATES);
$xssFilter = new Filters\XSS();
$compiler = new Compilers\Compiler($cache, $templateFactory, $xssFilter);
$template = new Views\Template("Hello, {{username}}");
$template->setTag("username", "Dave");
echo $compiler->compile($template); // "Hello, Dave"
```

## Caching
To improve the speed of template compiling, templates are cached using a class that implements `RDev\Views\Cache\ICache` (`RDev\Views\Cache\Cache` comes built-in to RDev).  You can specify how long a template should live in cache using `setLifetime()`.  If you do not want templates to live in cache at all, you can specify a non-positive lifetime.  If you'd like to create your own cache engine for templates, just implement `ICache` and pass it into your `Template` class.

#### Garbage Collection
Occasionally, you should clear out old cached template files to save disk space.  If you'd like to call it explicitly, call `gc()` on your cache object.  `Cache` has a mechanism for performing this garbage collection every so often.  You can customize how frequently garbage collection is run:
 
```php
use RDev\Files;
use RDev\Views;
use RDev\Views\Cache;

// Make 123 out of every 1,000 template compilations trigger garbage collection
$cache = new Cache\Cache(new Files\FileSystem(), "/tmp", 123, 1000);
```
Or use `setGCChance()`:
```php
// Make 1 out of every 500 template compilations trigger garbage collection
$cache->setGCChance(1, 500);
```

## Cross-Site Scripting
Tags are automatically sanitized to prevent cross-site scripting (XSS) when using the "{{" and "}}" tags.  To display unescaped data, simply use "{{!MY_UNESCAPED_TAG_NAME_HERE!}}".
##### Template
```
{{name}} vs {{!name!}}
```
##### Application Code
```php
$template->setContents($fileSystem->read(PATH_TO_HTML_TEMPLATE));
$template->setTag("name", "A&W");
echo $compiler->compile($template); // "A&amp;W vs A&W"
```

Alternatively, you can output a string literal inside tags:
##### Template
```
{{"A&W"}} vs {{!"A&W"!}}
```

This will output "A&amp;amp;W vs A&amp;W".

## Nesting Templates
Nesting templates is an easy way to keep two components reusable.  For example, many websites use a sidebar for navigation on most pages.  With **RDev**, you can create a template for the sidebar and another for all the pages' contents.  Then, you can combine a page with the sidebar using a tag:
##### Page Template
```
<div id="main">
    Here's my main content
</div>
{{!sidebar!}}
```
##### Sidebar Template
```
<div id="sidebar">
    <ul>
        <li><a href="/">Home</a></li>
        <li><a href="/logout">Log Out</a></li>
    </ul>
</div>
```
##### Application Code
```php
use RDev\Files;
use RDev\Views;

$sidebar = new Views\Template($fileSystem->read(PATH_TO_SIDEBAR_TEMPLATE));
$page = new Views\Template($fileSystem->read(PATH_TO_PAGE_TEMPLATE));
$page->setTag("sidebar", $compiler->compile($sidebar));
echo $compiler->compile($page);
```

##### Output
```
<div id="main">
    Here's my main content
</div>
<div id="sidebar">
    <ul>
        <li><a href="/">Home</a></li>
        <li><a href="/logout">Log Out</a></li>
    </ul>
</div>
```

> **Note:** It is recommended you use unescaped tags to nest templates that display HTML.  Otherwise, the HTML will be escaped and will not appear correctly.

## Extending Templates
Most templates extend some sort of master template.  To make your life easy, RDev builds support for this functionality into its templates.  RDev uses a *statement tag* `{% %}` for RDev-specific logic statements.  They provide the ability do such things as extend templates.

#### Example

##### Master.html
```
Hello, world!
```

##### Child
```
{% extend("Master.html") %}
Hi, Dave!
```

When the child template gets compiled, the `Master.html` template is automatically created by an `RDev\Views\Factories\ITemplateFactory` and inserted into the template to produce the following output:

```
Hello, world!
Hello, Dave!
```

> **Note:** When extending a template, the child template inherits all of the parent's tag and variable values.

#### Parts
Another common case is a master template that is leaving a child template to fill in some information.  For example, let's say our master has a sidebar, and we want to define the sidebar's contents in the child template:

##### Master.html
```
<div id="sidebar">
    {{sidebar}}
</div>
```

##### Child
```
{% extend("Master.html") %}
{% part("sidebar") %}
<ul>
    <li><a href="/">Home</a></li>
    <li><a href="/about">About</a></li>
</ul>
{% endpart %}
```

We created a *part* named "sidebar".  When the child gets compiled, the contents of that part will be inserted into a tag of the same name, which in this case is {{sidebar}}. We will get the following:

```
<div id="sidebar">
    <ul>
        <li><a href="/">Home</a></li>
        <li><a href="/about">About</a></li>
    </ul>
</div>
```

## Using PHP in Your Template
Keeping your view separate from your business logic is important.  However, there are times when it would be nice to be able to execute some PHP code to do things like for() loops to output a list.  There is no need to memorize library-specific constructs here.  With RDev's template system, you can do this:
##### Template
```
<ul><?php
foreach(["foo", "bar"] as $item)
{
    echo "<li>$item</li>";
}
?></ul>
```
##### Application Code
```php
$template->setContents($fileSystem->read(PATH_TO_HTML_TEMPLATE));
echo $compiler->compile($template); // "<ul><li>foo</li><li>bar</li></ul>"
```

You can also inject values from your application code into variables in your template:
##### Template
```
<?php if($isAdministrator): ?>
Hello, Administrator
<?php endif; ?>
```
##### Application Code
```php
$template->setContents($fileSystem->read(PATH_TO_HTML_TEMPLATE));
$template->setVar("isAdministrator", true);
echo $compiler->compile($template); // "Hello, Administrator"
```

> **Note:** PHP code is compiled first, followed by tags.  Therefore, you cannot use tags inside PHP.  However, it's possible to use the output of PHP code inside tags in your template.  Also, it's recommended to keep as much business logic out of the templates as you can.  In other words, utilize PHP in the template to simplify things like lists or basic if/else statements or loops.  Perform the bulk of the logic in the application code, and inject data into the template when necessary.

## Built-In Functions
#### PHP Functions
`RDev\Views\Compilers\Compiler` comes with built-in functions that you can call to format data in your template.  The following methods are built-in, and can be used in the exact same way that their native PHP counterparts are:
* `abs()`
* `ceil()`
* `count()`
* `date()`
* `floor()`
* `implode()`
* `json_encode()`
* `lcfirst()`
* `round()`
* `strtolower()`
* `strtoupper()`
* `substr()`
* `trim()`
* `ucfirst()`
* `ucwords()`
* `urldecode()`
* `urlencode()`

Here's an example of how to use a built-in function:
##### Template
```
4.35 rounded down to the nearest tenth is {{round(4.35, 1, PHP_ROUND_HALF_DOWN)}}
```
##### Application Code
```php
$template->setContents($fileSystem->read(PATH_TO_HTML_TEMPLATE));
echo $compiler->compile($template); // "4.35 rounded down to the nearest tenth is 4.3"
```
You can also pass variables into your functions in the template and set them using `setVar()`.

> **Note:**  Nested function calls (eg `trim(strtoupper(" foo "))`) are currently not supported.

#### RDev Functions
RDev also supplies some other built-in functions:
* `charset()`
  * Returns HTML used to select a character set
  * Accepts the following arguments:
    1. `string $charset` - The character set to use
* `css()`
  * Returns HTML used to link to a CSS stylesheet
  * Accepts the following arguments:
    1. `array|string $paths` - The path or list of paths to the stylesheets
* `favicon()`
  * Returns HTML used to display a favicon
  * Accepts the following arguments:
    1. `string $path` - The path to the favicon image
* `formatDateTime()`
  * Returns a formatted DateTime
  * Accepts the following arguments:
    1. `DateTime $dateTime` - The DateTime to format
    2. `string $format` - The optional format (defaults to "m/d/Y")
    3. `DateTimeZone|string $timeZone` - The optional DateTimeZone object or timezone identifier to use
* `httpEquiv()`
  * Returns HTML used to create an http-equiv attribute
  * Accepts the following arguments:
    1. `string $name` - The name of the http-equiv attribute, eg "refresh"
    2. `mixed $value` - The value of the attribute
* `metaDescription()`
  * Returns HTML used to display a meta description
  * Accepts the following arguments:
    1. `string $metaDescription` - The meta description to use
* `metaKeywords()`
  * Returns HTML used to display meta keywords
  * Accepts the following arguments:
    1. `array $metaKeywords` - The list of meta keywords to use
* `namedRouteURL()`
  * Returns a URL that is created using the rules of the input route name
  * Accepts the following arguments:
    1. `string $routeName` - The name of the route whose URL we're creating
    2. `array|mixed $args` - The arguments to pass into the `URLGenerator` to fill any host or path variables in the route ([learn more about the `URLGenerator`](/app/rdev/routing#url-generators))
* `pageTitle()`
  * Returns HTML used to display a title
  * Accepts the following arguments:
    1. `string $title` - The title to use
* `script()`
  * Returns HTML used to link to a script file
  * Accepts the following arguments:
    1. `array|string $paths` - The path or list of paths to the scripts
    2. `string $type` - The script type, eg "text/javascript"

Since these functions output HTML, use them inside unescaped tags.  Here's an example of how to use these functions:

##### Template
```
<!DOCTYPE html>
<html>
    <head>
        {{!charset("utf-8")!}}
        {{!httpEquiv("content-type", "text/html")!}}
        {{!pageTitle("My Website")!}}
        {{!metaDescription("An example website")!}}
        {{!metaKeywords(["RDev", "sample"])!}}
        {{!favicon("favicon.ico")!}}
        {{!css("stylesheet.css")!}}
    </head>
    <body>
        Hello, World!
        {{!script(["jquery.js", "angular.js"])!}}
    </body>
</html>
```

##### Application Code
```php
$template->setContents(TEMPLATE);
echo $compiler->compile($template);
```

This will output:

```
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="content-type" content="text/html">
        <title>My Website</title>
        <meta name="description" content="An example website">
        <meta name="keywords" content="RDev,sample">
        <link href="favicon.ico" rel="shortcut icon">
        <link href="stylesheet.css" rel="stylesheet">
    </head>
    <body>
        Hello, World!
        <script type="text/javascript" src="jquery.js"></script>
        <script type="text/javascript" src="angular.js"></script>
    </body>
</html>
```

It's recommended to inject the CSS and scripts into a template rather than declaring them in the template itself.  An easy way to do this to inject the list of CSS stylesheets and scripts into template variables:

##### Template
```
<!DOCTYPE html>
<html>
    <head>
        {{!css($headCSS)!}}
    </head>
    <body>
        Hello, World!
        {{!script($footerJS)!}}
    </body>
</html>
```

##### Application Code
```php
$template->setVar("headCSS", "stylesheet.css");
$template->setVar("footerJS", ["jquery.js", "angular.js"]);
```

This will output:

```
<!DOCTYPE html>
<html>
    <head>
        <link href="stylesheet.css" rel="stylesheet">
    </head>
    <body>
        Hello, World!
        <script type="text/javascript" src="jquery.js"></script>
        <script type="text/javascript" src="angular.js"></script>
    </body>
</html>
```

#### Using Template Functions in PHP Code
You may execute template functions in your PHP code by calling `RDev\Views\Compilers\ICompiler::executeTemplateFunction()`.  Let's take a look at an example that displays a pretty HTML page title formatted like `My Site | {NAME_OF_PAGE}`:

##### Template
```
<!DOCTYPE html>
<html>
    <head>
        {{!myPageTitle("About")!}}
    </head>
    <body>
        My About Page
    </body>
</html>
```

##### Application Code
```php
$compiler->registerTemplateFunction("myPageTitle", function($title) use ($compiler)
{
    // Take advantage of the built-in template function
    return $compiler->executeTemplateFunction("pageTitle", ["My Site | " . $title]);
});
$template->setContents(TEMPLATE);
echo $compiler->compile($template);
```

This will output:

```
<!DOCTYPE html>
<html>
    <head>
        <title>My Site | About</title>
    </head>
    <body>
        My About Page
    </body>
</html>
```

## Custom Template Functions
It's possible to add custom functions to your template.  For example, you might want to add a salutation to a last name in your template.  This salutation would need to know the last name, whether or not the person is a male, and if s/he is married.  You could set tags with the formatted value, but this would require a lot of duplicated formatting code in your application.  Instead, save yourself some work and register the function to the compiler:
##### Template
```
Hello, {{salutation("Young", false, true)}}
```
##### Application Code
```php
$template->setContents($fileSystem->read(PATH_TO_HTML_TEMPLATE));
// Our function simply needs to have a printable return value
$compiler->registerTemplateFunction("salutation", function($lastName, $isMale, $isMarried)
{
    if($isMale)
    {
        $salutation = "Mr.";
    }
    elseif($isMarried)
    {
        $salutation = "Mrs.";
    }
    else
    {
        $salutation = "Ms.";
    }

    return $salutation . " " . $lastName;
});
echo $compiler->compile($template); // "Hello, Mrs. Young"
```
> **Note:**  As with built-in functions, nested function calls are currently not supported.

## Extending the Compiler
Let's pretend that there's some unique feature or syntax you want to implement in your template that cannot currently be compiled with RDev's `Compiler`.  Using `Compiler::registerSubCompiler()`, you can compile the syntax in your template to the desired output.  RDev itself uses `registerSubCompiler()` to compile PHP and tags in templates.

Let's take a look at what should be passed into `registerSubCompiler()`:

  1. `RDev\Views\Compilers\SubCompilers\ISubCompiler $subCompiler`
  2. `int|null $priority`
    * If your sb-compiler needs to be executed before other compilers, simply pass in an integer to prioritize the sub-compiler (1 is the highest)
    * If you do not specify a priority, then the compiler will be executed after the prioritized sub-compilers in the order it was added

Let's take a look at an example that converts HTML comments to an HTML list of those comments:

```php
use RDev\Views;
use RDev\Views\Compilers\SubCompilers;

class MySubCompiler implements SubCompilers\ISubCompiler
{
    public function compile(Views\ITemplate $template, $content)
    {
        return "<ul>" . preg_replace("/<!--((?:(?!-->).)*)-->/", "<li>$1</li>", $content) . "</ul>";
    }
}

$compiler->registerSubCompiler(new MySubCompiler());
$template->setContents("<!--Comment 1--><!--Comment 2-->");
echo $compiler->compile($template); // "<ul><li>Comment 1</li><li>Comment 2</li></ul>"
```

## Escaping Tags
Want to escape a tag?  Easy!  Just add a backslash before the opening tag like so:
##### Template
```
Hello, {{username}}.  \{{I am escaped}}! \{{!Me too!}}. \{%So am I%}.
```
##### Application Code
```php
$template->setContents($fileSystem->read(PATH_TO_HTML_TEMPLATE));
$template->setTag("username", "Mr Schwarzenegger");
echo $compiler->compile($template); // "Hello, Mr Schwarzenegger.  {{I am escaped}}! {{!Me too!}}. {%So am I%}."
```

## Custom Tags
Want to use a custom character/string for the tags?  Easy!  Just specify it in the `Template` object like so:
##### Template
```
^^name$$ ++food--
```
##### Application Code
```php
$template->setContents($fileSystem->read(PATH_TO_HTML_TEMPLATE));
$template->setEscapedOpenTag("^^");
$template->setEscapedCloseTag("$$");
// You can also override the unescaped tags
$template->setUnescapedOpenTag("++");
$template->setUnescapedCloseTag("--");
// You can even override statement tags
$template->setStatementOpenTag("(*");
$template->setStatementCloseTag("*)");
// Try setting some tags
$template->setTag("name", "A&W");
$template->setTag("food", "Root Beer");
echo $compiler->compile($template); // "A&amp;W Root Beer"
```

## Template Factory
Having to always pass in the full path to load a template from a file can get annoying.  It can also make it more difficult to switch your template directory should you ever decide to do so.  This is where a `Factory` comes in handy.  Simply pass in a `FileSystem` and the directory that your templates are stored in, and you'll never have to repeat yourself:
 
```php
use RDev\Files;
use RDev\Views;
use RDev\Views\Factories;

$fileSystem = new Files\FileSystem();
// Assume we keep all templates at "/var/www/html/views"
$factory = new Factories\TemplateFactory($fileSystem, "/var/www/html/views");
// This creates a template from "/var/www/html/views/login.html"
$loginTemplate = $factory->create("login.html");
// This creates a template from "/var/www/html/views/books/list.html"
$bookListTemplate = $factory->create("books/list.html");
```
 
> **Note:** Preceding slashes in `create()` are not necessary.
 
#### Builders
 
Repetitive tasks such as setting up templates should not be done in controllers.  That should be left to dedicated classes called `Builders`.  A `Builder` is a class that does any setup on a template after it is created by the factory.  You can register a `Builder` to a template so that each time that template is loaded by the factory, the builders are run.  Register builders via `ITemplateFactory::registerBuilder()`.  The second parameter is a callback that returns an instance of your builder.  Builders are lazy-loaded (ie they're only created when they're needed), which is why a callback is passed instead of the actual instance.  Your builder classes must implement `RDev\Views\IBuilder`.  It's recommended that you register your builders via a [`Bootstrapper`](/app/rdev/applications#bootstrappers).

Let's take a look at an example:

```
<!-- Let's say this markup is in "Index.html" -->
<h1>{{siteName}}</h1>
{{content}}
```

```php
namespace MyApp\Builders;
use RDev\Files;
use RDev\Views;
use RDev\Views\Factories;

class MyBuilder implements Views\IBuilder
{
    public function build(Views\ITemplate $template)
    {
        $template->setTag("siteName", "My Website");
        
        return $template;
    }
}

// Register our builder to "Index.html"
$factory = new Factories\TemplateFactory(new Files\FileSystem(), __DIR__ . "/tmp");
$callback = function()
{
    return new MyBuilder();
};
$factory->registerBuilder("Index.html", $callback);

// Now, whenever we request "Index.html", the "siteName" tag will be set to "My Website"
$template = $factory->create("Index.html");
echo $template->getTag("siteName"); // "My Website"
```

#### Aliasing
Multiple pages might use the same template, but with different tag and variable values.  This creates a problem if we want to register a builder for one page that shares a template with others.  We don't want to register that builder for all the other pages that share the template.  This is where `ITemplateFactory::alias()` comes in handy.  You can create an alias, and then register builders to that alias.  `ITemplateFactory::create()` accepts either a template path or an alias.

```php
$templateFactory->alias("Home", "Master.html");
$templateFactory->alias("About", "Master.html");
$templateFactory->registerBuilder("Master.html", function()
{
    return new MasterBuilder();
});
$templateFactory->registerBuilder("Home", function()
{
    return new HomeBuilder();
});
$templateFactory->registerBuilder("About", function()
{
    return new AboutBuilder();
});
$masterTemplate = $templateFactory->create("Master.html"); // MasterBuilder is run
$homeTemplate = $templateFactory->create("Home"); // MasterBuilder and HomeBuilder are run
$aboutTemplate = $templateFactory->create("About"); // MasterBuilder and AboutBuilder are run
```

> **Note:** Builders registered to the template that an alias refers to will be run as well as builders registered to the alias.