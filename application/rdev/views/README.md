# Templates

## Table of Contents
1. [Introduction](#introduction)
2. [Basic Usage](#basic-usage)
3. [Cross-Site Scripting](#cross-site-scripting)
4. [Nesting Templates](#nesting-templates)
5. [Using PHP in Your Template](#using-php-in-your-template)
6. [Built-In Functions](#built-in-functions)
7. [Custom Functions](#custom-functions)
8. [Escaping Tags](#escaping-tags)
9. [Custom Tags](#custom-tags)

## Introduction
**RDev** has a template system, which is meant to simplify adding dynamic content to web pages.  You can inject data into your pages, create loops for generating iterative items, escape unsanitized text, and add your own tag extensions.  Unlike other popular template libraries out there, you can use plain old PHP for simple constructs such as if/else statements and loops.

## Basic Usage
##### Template
```
Hello, {{username}}
```
##### Application Code
```php
use RDev\Views\Templates;

$template = new Templates\Template();
$template->readFromFile(PATH_TO_HTML_TEMPLATE);
$template->setTag("username", "Beautiful Man");
echo $template->render(); // "Hello, Beautiful Man"
```

Alternatively, you could just render a template by passing it into `readFromInput()`:
```php
use RDev\Views\Templates;

$template = new Templates\Template();
$template->readFromInput("Hello, {{username}}");
$template->setTag("username", "Beautiful Man");
echo $template->render(); // "Hello, Beautiful Man"
```

## Cross-Site Scripting
Tags are automatically sanitized to prevent cross-site scripting (XSS) when using the "{{" and "}}" tags.  To display unescaped data, simply use "{{!MY_UNESCAPED_TAG_NAME_HERE!}}".
##### Template
```
{{name}} vs {{!name!}}
```
##### Application Code
```php
use RDev\Views\Templates;

$template = new Templates\Template();
$template->readFromFile(PATH_TO_HTML_TEMPLATE);
$template->setTag("name", "A&W");
echo $template->render(); // "A&amp;W vs A&W"
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
use RDev\Views\Templates;

$sidebar = new Templates\Template();
$sidebar->readFromFile(PATH_TO_SIDEBAR_TEMPLATE);
$page = new Templates\Template();
$page->readFromFile(PATH_TO_PAGE_TEMPLATE);
$page->setTag("sidebar", $sidebar->render());
echo $page->render();
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
use RDev\Views\Templates;

$template = new Templates\Template();
$template->readFromFile(PATH_TO_HTML_TEMPLATE);
echo $template->render(); // "<ul><li>foo</li><li>bar</li></ul>"
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
use RDev\Views\Templates;

$template = new Templates\Template();
$template->readFromFile(PATH_TO_HTML_TEMPLATE);
$template->setVar("isAdministrator", true);
echo $template->render(); // "Hello, Administrator"
```

> **Note:** PHP code is compiled first, followed by tags.  Therefore, you cannot use tags inside PHP.  However, it's possible to use the output of PHP code inside tags in your template.  Also, it's recommended to keep as much business logic out of the templates as you can.  In other words, utilize PHP in the template to simplify things like lists or basic if/else statements or loops.  Perform the bulk of the logic in the application code, and inject data into the template when necessary.

## Built-In Functions
Templates come with built-in functions that you can call to format data in your template.  The following methods are built-in, and can be used in the exact same way that their native PHP counterparts are:
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

RDev also supplies some other built-in functions:
* `formatDateTime()`
  * Returns a formatted date time
  * Accepts the following arguments:
    1. DateTime $dateTime - The DateTime to format
    2. string $format - The optional format (defaults to "m/d/Y")
    3. DateTimeZone|string $timeZone - The optional DateTimeZone object or timezone identifier to use

Here's an example of how to use a built-in function:
##### Template
```
4.35 rounded down to the nearest tenth is {{round(4.35, 1, PHP_ROUND_HALF_DOWN)}}
```
##### Application Code
```php
use RDev\Views\Templates;

$template = new Templates\Template();
$template->readFromFile(PATH_TO_HTML_TEMPLATE);
echo $template->render(); // "4.35 rounded down to the nearest tenth is 4.3"
```
You can also pass variables into your functions in the template and set them using `setVar()`.

> **Note:**  Nested function calls (eg `trim(strtoupper(" foo "))`) are currently not supported.

## Custom Functions
It's possible to add custom functions to your template.  For example, you might want to add a salutation to a last name in your template.  This salutation would need to know the last name, whether or not the person is a male, and if s/he is married.  You could set tags with the formatted value, but this would require a lot of duplicated formatting code in your application.  Instead, save yourself some work and register the function to the template:
##### Template
```
Hello, {{salutation("Young", false, true)}}
```
##### Application Code
```php
use RDev\Views\Templates;

$template = new Templates\Template();
$template->readFromFile(PATH_TO_HTML_TEMPLATE);
// Our function simply needs to have a printable return value
$template->registerFunction("salutation", function($lastName, $isMale, $isMarried)
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
echo $template->render(); // "Hello, Mrs. Young"
```
> **Note:**  As with built-in functions, nested function calls are currently not supported.

## Escaping Tags
Want to escape a tag?  Easy!  Just add a backslash before the opening tag like so:
##### Template
```
Hello, {{username}}.  \{{I am escaped}}! \{{!Me too!}}!
```
##### Application Code
```php
use RDev\Views\Templates;

$template = new Templates\Template();
$template->readFromFile(PATH_TO_HTML_TEMPLATE);
$template->setTag("username", "Mr Schwarzenegger");
echo $template->render(); // "Hello, Mr Schwarzenegger.  {{I am escaped}}! {{!Me too!}}!"
```

## Custom Tags
Want to use a custom character/string for the tags?  Easy!  Just specify it in the `Template` object like so:
##### Template
```
^^name$$ ++food--
```
##### Application Code
```php
use RDev\Views\Templates;

$template = new Templates\Template();
$template->readFromFile(PATH_TO_HTML_TEMPLATE);
$template->setEscapedOpenTag("^^");
$template->setEscapedCloseTag("$$");
// You can also override the unescaped tags
$template->setUnescapedOpenTag("++");
$template->setUnescapedCloseTag("--");
$template->setTag("name", "A&W");
$template->setTag("food", "Root Beer");
echo $template->render(); // "A&amp;W Root Beer"
```