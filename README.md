# RDev
[![Build Status](https://travis-ci.org/ramblingsofadev/RDev.svg?branch=master)](https://travis-ci.org/ramblingsofadev/RDev)
[![Latest Stable Version](https://poser.pugx.org/rdev/rdev/v/stable.svg)](https://packagist.org/packages/rdev/rdev)
[![Latest Unstable Version](https://poser.pugx.org/rdev/rdev/v/unstable.svg)](https://packagist.org/packages/rdev/rdev)
[![License](https://poser.pugx.org/rdev/rdev/license.svg)](https://packagist.org/packages/rdev/rdev)
## Table of Contents
### About RDev
1. [Introduction](#introduction)
2. [Requirements](#requirements)
3. [Installation](#installation)
4. [Documentation](#documentation)
5. [Requirements](#requirements)
6. [License](#license)
7. [History](#history)
8. [About the Author](#about-the-author)

### Building Your First Application
1. [Setting Up an Application](https://github.com/ramblingsofadev/docs/blob/master/application.md)

### Templates
1. [Building a Template](https://github.com/ramblingsofadev/docs/blob/master/views.md)

### Routing
1. [Setting Up a Router](https://github.com/ramblingsofadev/docs/blob/master/routing.md)

### Databases
1. [Relational Databases](https://github.com/ramblingsofadev/docs/blob/master/rdbms.md)
  1. [Type Mappers](https://github.com/ramblingsofadev/docs/blob/master/typemappers.md)
2. [NoSQL Databases](https://github.com/ramblingsofadev/docs/blob/master/nosql.md)
3. [Object-Relational Mapping](https://github.com/ramblingsofadev/docs/blob/master/orm.md)
4. [Query Builders](https://github.com/ramblingsofadev/docs/blob/master/querybuilders.md)

### Inversion of Control
1. [Dependency Injection](https://github.com/ramblingsofadev/docs/blob/master/ioc.md)

### File System
1. [File System](https://github.com/ramblingsofadev/docs/blob/master/files.md)

### HTTP Requests and Responses
1. [HTTP Requests and Responses](https://github.com/ramblingsofadev/docs/blob/master/http.md)

### Console Applications
1. [Running Console Applications](https://github.com/ramblingsofadev/docs/blob/master/console.md)

## Introduction
**RDev** is a PHP web application framework that simplifies the difficult parts of creating and maintaining a secure, scalable website.  With RDev, things like database management, caching, ORM, page templates, and routing are a cinch.  It was written with customization, performance, and best-practices in mind.  Thanks to test-driven development (TDD), the framework is reliable and thoroughly tested. RDev is split into components, which can be installed separately or bundled together.

## Installation
RDev can be installed using Composer:

```
composer create-project rdev/project --prefer-dist
```

You can also install the components of RDev individually.  The following is a list of all the components available for installation:

```
rdev/authentication
rdev/console
rdev/cryptography
rdev/databases
rdev/files
rdev/http
rdev/ioc
rdev/orm
rdev/sessions
rdev/users
rdev/views
```

## Documentation
For complete documentation, [visit the docs repository](https://github.com/ramblingsofadev/docs).

## Requirements
* PHP 5.5, 5.6, or HHVM >= 3.4.0
* OpenSSL
* mbstring
* A default PHP timezone set in the PHP.ini

## License
This software is licensed under the MIT license.  Please read the LICENSE for more information.

## History
**RDev** is written and maintained by David Young.  It started as a simple exercise to write a RESTful API router in early 2014, but it quickly turned into something else.  At my 9-5 job, I was struggling with complex SQL queries that were being concatenated/Frankenstein'd together, depending on various conditions.  I decided to write "query builder" classes that could programmatically build queries for me, which greatly simplified my problem at work.  The more I worked on the simple features, the more I got interested into diving into the more complex stuff like ORM.  Before I knew it, I had developed a suite of tools, which are coming together to become the framework that is **RDev**.  Why "RDev"?  My Twitter handle is [@RamblingsOfADev](https://www.twitter.com/ramblingsofadev), which is also my GitHub name.  At first, I went with "RamODev", but I couldn't shake accidentally calling it "DevORama", so I shortened it to "RDev".

## About the Author
I am a professional software developer and flight instructor.  I went to the University of Illinois at Urbana-Champaign and graduated with a degree in Math and Computer Science.  While in college, I obtained my commercial pilot license as well as my flight instructor licenses (CFI/CFII/MEI).  I'm active in [Angel Flight](http://angelflightcentral.org/), a charity organization that offers free flights to seriously ill people, in which I am a volunteer pilot.  When I'm not flying, you can find me playing classical piano, reading books about programming, writing code, or doing something in the great outdoors. My favorite books are:
* "[Code Complete](http://www.amazon.com/Code-Complete-Practical-Handbook-Construction/dp/0735619670)"
* "[T. rex and the Crater of Doom](http://www.amazon.com/Crater-Doom-Princeton-Science-Library/dp/0691131031)"
* "[The Demon-Haunted World: Science as a Candle in the Dark](http://www.amazon.com/The-Demon-Haunted-World-Science-Candle/dp/0345409469)"