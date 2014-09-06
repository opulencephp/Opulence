# Templates

## Table of Contents
1. [Introduction](#introduction)
2. [Basic Usage](#basic-usage)
3. [Nesting Templates](#nesting-templates)
4. [Cross-Site Scripting](#cross-site-scripting)
5. [Using PHP in Your Template](#using-php-in-your-template)
6. [Built-In Functions](#built-in-functions)
7. [Custom Functions](#custom-functions)
8. [Escaping Tags](#escaping-tags)
9. [Custom Tag Placeholders](#custom-tag-placeholders)

## Introduction
**RDev** has a template system, which is meant to simplify adding dynamic content to web pages.  You can inject data into your pages, create loops for generating iterative items, escape unsanitized text, and add your own tag extensions.

## Basic Usage
#### Template
```
Hello, {{username}}
```
#### Application Code
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

## Nesting Templates
Nesting templates is an easy way to keep two components reusable.  For example, many websites use a sidebar for navigation on most pages.  With **RDev**, you can create a template for the sidebar and another for all the pages' contents.  Then, you can combine a page with the sidebar using a tag:
#### Page Template
```
<div id="main">
    Here's my main content
</div>
{{sidebar}}
```
#### Sidebar Template
```
<div id="sidebar">
    <ul>
        <li><a href="/">Home</a></li>
        <li><a href="/logout">Log Out</a></li>
    </ul>
</div>
```
#### Application Code
```php
use RDev\Views\Templates;

$sidebar = new Templates\Template(PATH_TO_SIDEBAR_TEMPLATE);
$page = new Templates\Template(PATH_TO_PAGE_TEMPLATE);
$page->setTag("sidebar", $sidebar->render());
echo $page->render();
```

#### Output
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

## Cross-Site Scripting
To sanitize data to prevent cross-site scripting (XSS), simply use the triple-brace syntax:
#### Template
```
{{{namesOfCouple}}}
```
#### Application Code
```php
use RDev\Views\Templates;

$template = new Templates\Template(PATH_TO_HTML_TEMPLATE);
$template->setTag("namesOfCouple", "Dave & Lindsey");
echo $template->render(); // "Dave &amp; Lindsey"
```

Alternatively, you can output a string literal inside the triple-braces:
#### Template
```
{{{"Dave & Lindsey"}}}
```

This will output "Dave &amp;amp; Lindsey".  

## Using PHP in Your Template
Keeping your view separate from your business logic is important.  However, there are times when it would be nice to be able to execute some PHP code to do things like for() loops to output a list.  With RDev's template system, you can do this:
#### Template
```
<ul><?php
foreach(["foo", "bar"] as $item)
{
    echo "<li>$item</li>";
}
?></ul>
```
#### Application Code
```php
use RDev\Views\Templates;

$template = new Templates\Template(PATH_TO_HTML_TEMPLATE);
echo $template->render(); // "<ul><li>foo</li><li>bar</li></ul>"
```

You can also inject values from your application code into variables in your template:
#### Template
```
<?php if($isAdministrator): ?>
Hello, Administrator
<?php endif; ?>
```
#### Application Code
```php
use RDev\Views\Templates;

$template = new Templates\Template();
$template->readFromFile(PATH_TO_HTML_TEMPLATE);
$template->setVar("isAdministrator", true);
echo $template->render(); // "Hello, Administrator"
```

*Note*: PHP code is compiled first, followed by tags.  Therefore, you cannot use tags inside PHP.  However, it's possible to use the output of PHP code inside tags in your template.  Also, it's recommended to keep as much business logic out of the templates as you can.  In other words, utilize PHP in the template to simplify things like lists or basic if/else statements or loops.  Perform the bulk of the logic in the application code, and inject data into the template when necessary.

## Built-In Functions
Templates come with built-in functions that you can call to format data in your template.  The following methods are built-in, and can be used in the exact same way that their native PHP counterparts are:
* `abs()`
* `ceil()`
* `count()`
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

The built-in method `date()` behaves differently than its PHP counterpart.  It accepts a DateTime, an optional format, and an optional time zone.  It returns the DateTime with the specified format.

Here's an example of how to use a built-in function:
#### Template
```
4.35 rounded down to the nearest tenth is {{round(4.35, 1, PHP_ROUND_HALF_DOWN)}}
```
#### Application Code
```php
use RDev\Views\Templates;

$template = new Templates\Template(PATH_TO_HTML_TEMPLATE);
echo $template->render(); // "4.35 rounded down to the nearest tenth is 4.3"
```
Note that nested function (eg `trim(strtoupper("foo "))`) calls are currently not supported.

## Custom Functions
It's possible to add custom functions to your template.  For example, you might want to add a salutation to a last name in your template.  This salutation would need to know the last name, whether or not the person is a male, and if s/he is married.  You could set tags with the formatted value, but this would require a lot of duplicated formatting code in your application.  Instead, save yourself some work and register the function to the template:
#### Template
```
Hello, {{salutation("Young", false, true)}}
```
#### Application Code
```php
use RDev\Views\Templates;

$template = new Templates\Template(PATH_TO_HTML_TEMPLATE);
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
Note that, as with built-in functions, nested function calls are currently not supported.

## Escaping Tags
Want to escape a tag?  Easy!  Just add a backslash before the opening tag like so:
#### Template
```
Hello, {{username}}.  \{{I am escaped}}! \{{{Me too}}}!
```
#### Application Code
```php
use RDev\Views\Templates;

$template = new Templates\Template();
$template->readFromFile(PATH_TO_HTML_TEMPLATE);
$template->setTag("username", "Mr Schwarzenegger");
echo $template->render(); // "Hello, Mr Schwarzenegger.  {{I am escaped}}! {{{Me too}}}!"
```

## Custom Tag Placeholders
Want to use a custom character/string for the tag placeholders?  Easy!  Just specify it in the `Template` object like so:
#### Template
```
Hello, ^^username$$
```
#### Application Code
```php
use RDev\Views\Templates;

$template = new Templates\Template();
$template->readFromFile(PATH_TO_HTML_TEMPLATE);
$template->setOpenTagPlaceholder("^^");
$template->setCloseTagPlaceholder("$$");
$template->setTag("username", "Daft Punk");
echo $template->render(); // "Hello, Daft Punk"
```