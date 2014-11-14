# Routing

## Table of Contents
1. [Introduction](#introduction)
2. [Basic Usage](#basic-usage)
  1. [Multiple Methods](#multiple-methods)
3. [Route Variables](#route-variables)
  1. [Regular Expressions](#regular-expressions)
  2. [Optional Variables](#optional-variables)
  3. [Default Values](#default-values)
4. [Host Matching](#host-matching)
5. [Filters](#filters)
6. [HTTPS](#https)
7. [Named Routes](#named-routes)
8. [Route Grouping](#route-grouping)
  1. [Controller Namespaces](#controller-namespaces)
  2. [Group Filters](#group-filters)
  3. [Group Hosts](#group-hosts)
  4. [Group HTTPS](#group-https)
9. [URL Generators](#url-generators)
10. [Missing Routes](#missing-routes)
11. [Notes](#notes)

## Introduction
So, you've made some page templates, and you've written some models.  Now, you need a way to wire everything up so that users can access your pages.  To do this, you need a `Router` and controllers.  The `Router` can capture data from the URL to help you decide which controller to use and what data to send to the view.  It makes building a RESTful application a cinch.

## Basic Usage
Routes require a few pieces of information:
* The path the route is valid for
* The HTTP method (eg "GET", "POST", "DELETE", or "PUT") the route is valid for
* The fully-qualified name of the controller class to use
* The name of the method in that controller to call to render the view

Let's take a look at a simple route that maps a GET request to the path "/users":
```php
use RDev\IoC;
use RDev\Routing;
use RDev\Routing\Compilers;

$container = new IoC\Container();
$router = new Routing\Router($container, new Routing\Dispatcher($container), new Compilers\Compiler());
// This will route a GET request to "/users" to MyController->getAllUsers()
$router->get("/users", ["controller" => "MyApp\\MyController@getAllUsers"]);
// This will route a POST request to "/login" to MyController->login()
$router->post("/login", ["controller" => "MyApp\\MyController@login"]);
// This will route a DELETE request to "/users/me" to MyController->deleteUser()
$router->delete("/users/me", ["controller" => "MyApp\\MyController@deleteUser"]);
// This will route a PUT request to "/users/profile/image" to MyController->uploadProfileImage()
$router->put("/users/profile/image", ["controller" => "MyApp\\MyController@uploadProfileImage"]);
```

The router takes advantage of the [Dependency Injection Container](/app/rdev/ioc) to instantiate your controller.

> **Note:** Primitives (eg strings and arrays) should not appear in a controller's constructor because the IoC container would have no way of resolving those dependencies at runtime.  Stick to type-hinted objects in the constructors.

#### Multiple Methods
You can register a route to multiple methods using the router's `multiple()` method:
```php
$router->multiple(["GET", "POST"], ["controller" => "MyApp\\MyController@myMethod"]);
```

To register a route for all methods, use the `any()` method:
```php
$router->any(["controller" => "MyApp\\MyController@myMethod"]);
```

## Route Variables
Let's say you want to grab a specific user's profile page.  You'll probably want to structure your URL like "/users/{userId}/profile", where "{userId}" is the Id of the user whose profile we want to view.  Using a `Router`, the data matched in "{userId}" will be mapped to a parameter in your controller's method named "$userId".

> **Note**: All non-optional parameters in the controller method must have identically-named route variables.  In other words, if your method looks like `function showBook($authorName, $bookTitle = null)`, your path must have a "{authorName}" variable.  The routes "/authors/{authorName}/books" and "/authors/{authorName}/books/{bookTitle}" would be valid, but "/authors" would not.

Let's take a look at a full example:
```php
use RDev\Routing;

class UserController extends Routing\Controller
{
    public function showProfile($userId)
    {
        return "Profile for user " . $userId;
    }
}

$router->get("/users/{userId}/profile", ["controller" => "MyApp\\UserController@showProfile"]);
```

Calling the path "/users/23/profile" will return "Profile for user 23".

#### Regular Expressions
If you'd like to enforce certain rules for a route variable, you may do so in the options array.  Simply add a "variables" entry with variable names-to-regular-expression mappings:
```php
$router->get("/users/{userId}/profile", [
    "controller" => "MyApp\\UserController@showProfile",
    "variables" => [
        "userId" => "\d+" // The user Id variable must now be a number
    ]
]);
```

#### Optional Variables
If a certain variable is optional, simply append "?" to it:
```php
$router->get("/books/{bookId?}", ["controller" => "MyApp\\BookController@showBook"]);
```

This would match both "/books/" and "/books/23".

#### Default Values
Sometimes, you might want to have a default value for a route variable.  Doing so is simple:
```php
$router->get("/food/{foodName=all}", ["controller" => "MyApp\\FoodController@showFood"]);
```

If no food name was specified, "all" will be the default value.

> **Note:** To give an optional variable a default value, structure the route variable like "{varName?=value}".

## Host Matching
Routers can match on hosts as well as paths.  Want to match calls to a subdomain?  Easy:

```php
$routeOptions = [
    "controller" => "MyApp\\InboxController@showInbox",
    "host" => "mail.mysite.com" 
];
$router->get("/inbox", $routeOptions);
```

Just like with paths, you can create variables from components of your host.  In the following example, a variable called `$subdomain` will be passed into `MyApp\SomeController::doSomething()`:

```php
$routeOptions = [
    "controller" => "MyApp\\SomeController@doSomething",
    "host" => "{subdomain}.mysite.com" 
];
$router->get("/foo", $routeOptions);
```

Host variables can also have regular expression constraints, similar to path variables.

## Filters
Some routes might require actions to occur before and after the controller is called.  For example, you might want to check if a user is authenticated before allowing him or her access to a certain page.  This is when filters come in handy.  "Pre" filters are executed before the controller is called, and "post" filters are called after the controller.  Here is the order of precedence of return values of filters and controllers:

1. If a pre-filter returns something other than null, it is returned by the router, and the controller is never called
2. If a post-filter returns something other than null, it is returned by the router
3. If the pre- and post-filters do not return anything, then the controller's output is returned
4. If the controller does not return anything, then an empty response is returned

Filters are specified in the route options.  They must contain the fully-qualified name of the filter class.  The class itself must implement `RDev\Routing\Filters\IFilter`, which has a `run()` method where the filtering is performed.
```php
namespace MyApp;
use RDev\HTTP;
use RDev\Routing;
use RDev\Routing\Filters;

class Authenticate implements Filters\IFilter
{
    public function run(Routing\Route $route, HTTP\Request $request, HTTP\Response $response = null)
    {
        if(!MyApp::isUserLoggedIn())
        {
            return new HTTP\RedirectResponse("/login");
        }
    }
}

$router->post("/users/posts", [
    "controller" => "MyApp\\UserController@createPost",
    "pre" => "MyApp\\Authenticate" // Could also be an array of pre-filters
]);
```

Now, the `Authenticate` filter will be run before the `createPost()` method is called.  If the user is not logged in, he'll be redirected to the login page.  To apply "post" filters to a route, just add a "post" entry in the route options.  In post-filters, the response of the previous filters is passed into the next filters, allowing you to chain together actions on the response.

## HTTPS
Some routes should only match on an HTTPS connection.  To do this, set the `https` flag to true in the options:

```php
$options = [
    "controller" => "MyApp\\MyController@myMethod",
    "https" => true
];
$router->get("/users", $options);
```

HTTPS requests to "/users" will match, but non SSL connections will return a 404 response.

## Named Routes
Routes can be given a name, which makes them identifiable.  This is especially useful for things like generating URLs from a route.  To name a route, pass a `"name" => "THE_NAME"` into the route options:

```php
$options = [
    "controller" => "MyApp\\MyController@myMethod",
    "name" => "awesome"
];
$router->get("/users", $options);
```

This will create a route named "awesome".

## Route Grouping
One of the most important sayings in programming is "Don't repeat yourself" or "DRY".  In other words, don't copy-and-paste code because that leads to difficulties in maintaining/changing the code base in the future.  Let's say you have several routes that start with the same path.  Instead of having to write out the full path for each route, you can create a group:
```php
$router->group(["path" => "/users/{userId}"], function() use ($router)
{
    $router->get("/profile", ["controller" => "MyApp\\UserController@showProfile"]);
    $router->delete("", ["controller" => "MyApp\\UserController@deleteUser"]);
});
```

Now, a GET request to "/users/{userId}/profile" will get a user's profile, and a DELETE request to "/users/{userId}" will delete a user.

#### Controller Namespaces
If all the controllers in a route group belong under a common namespace, you can specify the namespace in the group options:
```php
$router->group(["controllerNamespace" => "MyApp\\Controllers"], function() use ($router)
{
    $router->get("/users", ["controller" => "UserController@showAllUsers"]);
    $router->get("/posts", ["controller" => "PostController@showAllPosts"]);
});
```

Now, a GET request to "/users" will route to `MyApp\Controllers\UserController::showAllUsers()`, and a GET request to "/posts" will route to `MyApp\Controllers\PostController::showAllPosts()`.

#### Group Filters
Route groups allow you to apply "pre" and "post" filters to multiple routes:
```php
$router->group(["pre" => "MyApp\\Authenticate"], function() use ($router)
{
    $router->get("/users/{userId}/profile", ["controller" => "MyApp\\UserController@showProfile"]);
    $router->get("/posts", ["controller" => "MyApp\\PostController@showPosts"]);
});
```

The `Authenticate` filter will be executed on any matched routes inside the closure.

#### Group Hosts
You can filter by host in router groups:

```php
$router->group(["host" => "google.com"], function() use ($router)
{
    $router->get("/", ["controller" => "MyApp\\HomeController@showHomePage"]);
    $router->group(["host" => "mail."], function() use ($router)
    {
        $router->get("/", ["controller" => "MyApp\\MailController@showInbox"]);
    });
});
```

> **Note:** When specifying hosts in nested router groups, the inner groups' hosts are prepended to the outer groups' hosts.  This means the inner-most route in the example above will have a host of "mail.google.com".

#### Group HTTPS
You can force all routes in a group to be HTTPS:

```php
$router->group(["https" => true], function() use ($router)
{
    $router->get("/", ["controller" => "MyApp\\HomeController@showHomePage"]);
    $router->get("/books", ["controller" => "MyApp\\BookController@showBooksPage"]);
});
```

> **Note:** If the an outer group marks the routes HTTPS but an inner one doesn't, the inner group gets ignored.  The outer-most group with an HTTPS definition is the only one that counts.

## Missing Routes
In the case that the router cannot find a route that matches the request, a 404 response will be returned.  If you'd like to customize your 404 page or any other HTTP error status page, override `showHTTPError()` in your controller and display the appropriate response.  Register your controller in the case of a missing route using `Router::setMissedRouteControllerName()`:

Then, just add a route to handle this:
```php
namespace MyApp;
use RDev\HTTP;
use RDev\Routing;

class MyController extends Routing\Controller
{
    public function showHTTPError($statusCode)
    {
        switch($statusCode)
        {
            case HTTP\ResponseHeaders::HTTP_NOT_FOUND:
                return new HTTP\Response("My custom 404 page", $statusCode);
            default:
                return new HTTP\Response("Something went wrong", $statusCode);
        }
    }
}

$router->setDefaultControllerClass("MyApp\\MyController");
// Assume $request points to a request object with a path that isn't covered in the router
$router->route($request); // returns a 404 response with "My custom 404 page"
```

## URL Generators
A cool feature is the ability to generate URLs from named routes using `RDev\Routing\URL\URLGenerator`.  If your route has variables in the domain or path, you just pass them in `URLGenerator::generate()`.  Unless a host is specified in the route, an absolute path is generated:

```php
use RDev\Routing;
use RDev\Routing\Compilers;
use RDev\Routing\URL;

$compiler = new Compilers\Compiler();
$urlGenerator = new URL\URLGenerator($compiler);
// Let's assume the router is already instantiated
// Let's add a route named "profile"
$router->get("/users/{userId}", ["controller" => "MyApp\\ProfileController@showProfile", "name" => "profile"]);
// Now we can generate a URL and pass in data to it
echo $urlGenerator->generate("profile", 23); // "/users/23"
```

If we specify a host in our route, an absolute URL is generated.  We can even define variables in the host:

```php
// Let's assume the URL generator is already instantiated
// Let's add a route named "inbox"
$routeOptions = [
    "controller" => "MyApp\\InboxController@showInbox",
    "host" => "{country}.mail.example.com",
    "name" => "inbox"
];
$router->get("/users/{userId}", $routeOptions);
// Any values passed in will first be used to define variables in the host
// Any leftover values will define the values in the path
echo $urlGenerator->generate("inbox", "us", 724); // "http://us.mail.example.com/users/724"
```

Secure routes with hosts specified will generate `https://` absolute URLs.

> **Note:** If you do not define all the non-optional variables in the host or domain, a `URLException` will be thrown.

## Notes
Routes are matched based on the order they were added to the router.  So, if you did the following:
```php
$router->get("/{foo}", [
    "controller" => "MyApp\\MyController@myMethod",
    "variables" => [
        "foo" => ".*"
    ]
]);
$router->get("/users", ["controller" => "MyApp\\MyController@myMethod"]);
```

...The first route "/{foo}" would always match first because it was added first.  Add any "fall-through" routes after you've added the rest of your routes.