# Templates

## Table of Contents
1. [Introduction](#introduction)
2. [Basic Usage](#basic-usage)
3. [Nesting Templates](#nesting-templates)
4. [Cross-Site Scripting](#cross-site-scripting)
5. [Using PHP in Your Template](#using-php-in-your-template)
6. [Custom Functions](#custom-functions)
7. [Escaping Tags](#escaping-tags)
8. [Custom Tag Placeholders](#custom-tag-placeholders)

## Introduction
**RDev** has a template system, which is meant to simplify adding dynamic content to web pages.  You can inject data into your pages, create loops for generating iterative items, escape unsanitized text, and add your own tag extensions.

## Basic Usage
#### Template
```
Hello, {{username}}
```
#### Application Code
```php
use RDev\Views\Pages;

$template = new Pages\Template();
$template->readFromFile(PATH_TO_HTML_TEMPLATE);
$template->setTag("username", "Beautiful Man");
echo $template->render(); // "Hello, Beautiful Man"
```

Alternatively, we could just render a template by passing it into `readFromInput()`:
```php
use RDev\Views\Pages;

$template = new Pages\Template();
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
use RDev\Views\Pages;

$sidebar = new Pages\Template(PATH_TO_SIDEBAR_TEMPLATE);
$page = new Pages\Template(PATH_TO_PAGE_TEMPLATE);
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
use RDev\Views\Pages;

$template = new Pages\Template(PATH_TO_HTML_TEMPLATE);
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
use RDev\Views\Pages;

$template = new Pages\Template(PATH_TO_HTML_TEMPLATE);
echo $template->render(); // "<ul><li>foo</li><li>bar</li></ul>"
```

You can also inject values from your application code into variables in your template:
#### Template
```
<?php if($isAdministrator): ?>
<a href="admin.php">Admin</a>
<?php endif; ?>
```
#### Application Code
```php
use RDev\Views\Pages;

$template = new Pages\Template();
$template->readFromFile(PATH_TO_HTML_TEMPLATE);
$template->setVar("isAdministrator", true);
echo $template->render(); // "<a href=\"admin.php\">Admin</a>"
```

*Note*: PHP code is compiled first, followed by tags.  Therefore, it's possible to use the output of PHP code inside tags in your template.  Also, it's recommended to keep as much business logic out of the templates as you can.  In other words, utilize PHP in the template to simplify things like lists or basic if/else statements or loops.  Perform the bulk of the logic in the application code, and inject data into the template when necessary.

## Custom Functions
It's possible to add custom functions to your template.  For example, you might want to output a formatted DateTime throughout your template.  You could set tags with the formatted values, but this would require a lot of duplicated formatting code in your application.  Instead, save yourself some work and add a compiler:
#### Template
```
{{dateFormatter($greatDay, "m/d/Y")}} is a great day
```
#### Application Code
```php
use RDev\Views\Pages;

$template = new Pages\Template(PATH_TO_HTML_TEMPLATE);
$template->registerCompiler(function($content) use ($template)
{
    return preg_replace($template->getFunctionMatcher("dateFormatter"), "<?php echo $1->format($2); ?>", $content);
});
$greatDay = \DateTime::createFromFormat("m/d/Y", "07/24/1987");
$template->setVar("greatDay", $greatDay);
echo $template->render(); // "07/24/1987 is a great day"
```

Note that arguments passed into the function in the template are backreferenced by the function matcher.  They are available in the replacement string starting with $1 for argument 1, $2 for argument 2, etc.

## Escaping Tags
Want to escape a tag?  Easy!  Just add a backslash before the opening tag like so:
#### Template
```
Hello, {{username}}.  \{{I am escaped}}! \{{{Me too}}}!
```
#### Application Code
```php
use RDev\Views\Pages;

$template = new Pages\Template();
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
use RDev\Views\Pages;

$template = new Pages\Template();
$template->readFromFile(PATH_TO_HTML_TEMPLATE);
$template->setOpenTagPlaceholder("^^");
$template->setCloseTagPlaceholder("$$");
$template->setTag("username", "Daft Punk");
echo $template->render(); // "Hello, Daft Punk"
```