# Opulence
[![Build Status](https://travis-ci.org/opulencephp/Opulence.svg?branch=master)](https://travis-ci.org/opulencephp/Opulence)
[![Latest Stable Version](https://poser.pugx.org/opulence/opulence/v/stable.svg)](https://packagist.org/packages/opulence/opulence)
[![Latest Unstable Version](https://poser.pugx.org/opulence/opulence/v/unstable.svg)](https://packagist.org/packages/opulence/opulence)
[![License](https://poser.pugx.org/opulence/opulence/license.svg)](https://packagist.org/packages/opulence/opulence)

## Introduction
**Opulence** is a PHP web application framework that simplifies the difficult parts of creating and maintaining a secure, scalable website.  With Opulence, things like database management, caching, ORM, page templates, and routing are a cinch.  It was written with customization, performance, and best-practices in mind.  Thanks to test-driven development (TDD), the framework is reliable and thoroughly tested. Opulence is split into components, which can be installed separately or bundled together.

## Installation
Opulence can be installed using Composer:

```
composer create-project opulence/project --prefer-dist
```

## Documentation
For complete documentation, <a href="https://www.opulencephp.com" target="_blank">visit the official website</a>.

## Requirements
* PHP 7.0, or HHVM >= 3.4.0
* OpenSSL
* mbstring
* A default PHP timezone set in the PHP.ini

## License
This software is licensed under the MIT license.  Please read the LICENSE for more information.

## History
**Opulence** is written and maintained by David Young.  It started as a simple exercise to write a RESTful API router in early 2014, but it quickly turned into something else.  At my 9-5 job, I was struggling with complex SQL queries that were being concatenated/Frankenstein'd together, depending on various conditions.  I decided to write "query builder" classes that could programmatically build queries for me, which greatly simplified my problem at work.  The more I worked on the simple features, the more I got interested into diving into the more complex stuff like ORM.  Before I knew it, I had developed a suite of tools, which are coming together to become the framework that was originally named **RDev**.  I used the name until mid-July 2015 when I decided to give it a proper name.  Why "Opulence"?  It's feature-rich, that's why.  Maybe it'll make you rich one day.