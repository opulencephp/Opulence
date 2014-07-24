# Templates
**RDev** has a template system, which is meant to simplify adding dynamic content to web pages.  Simply create an HTML file with tags and then specify their values:
HTML Template:
```
Hello, {{username}}
```
```php
use RDev\Views\Pages;

$template = new Pages\Template(PATH_TO_HTML_TEMPLATE);
$template->setTag("username", "Beautiful Man");
echo $template->getOutput(); // "Hello, Beautiful Man"
```

## Escaping Tags
Want to escape a tag?  Easy!  Just add a backslash before the opening tag like so:
```
Hello, {{username}}.  \{{I am escaped}}!
```
```php
use RDev\Views\Pages;

$template = new Pages\Template(PATH_TO_HTML_TEMPLATE);
$template->setTag("username", "Mr Schwarzenegger");
echo $template->getOutput(); // "Hello, Mr Schwarzenegger.  {{I am escaped}}!"
```

## Custom Tag Placeholders
Want to use a custom character/string for the tag placeholders?  Easy!  Just specify it in the *Template* object like so:
```
Hello, ^^username$$
```
```php
use RDev\Views\Pages;

$template = new Pages\Template(PATH_TO_HTML_TEMPLATE);
$template->setOpenTagPlaceholder("^^");
$template->setCloseTagPlaceholder("$$");
$template->setTag("username", "Daft Punk");
echo $template->getOutput(); // "Hello, Daft Punk"
```
