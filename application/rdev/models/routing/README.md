# Routing

## Table of Contents
1. [Introduction](#introduction)
2. [Basic Usage](#basic-usage)
  1. [Multiple Methods](#multiple-methods)
3. [Route Variables](#route-variables)
  1. [Regular Expressions](#regular-expressions)
  2. [Optional Variables](#optional-variables)
  3. [Default Values](#default-values)
4. [Filters](#filters)
5. [Route Grouping](#route-grouping)
  1. [Controller Namespaces](#controller-namespaces)
  1. [Group Filters](#group-filters)
6. [Notes](#notes)

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
use RDev\Models\HTTP;
use RDev\Models\IoC;
use RDev\Models\Routing;

$router = new Routing\Router(new IoC\Container(), new HTTP\Connection());
// This will route a GET request to "/users" to MyController->getAllUsers()
$router->get("/users", ["controller" => "MyApp\\MyController@getAllUsers"]);
// This will route a POST request to "/login" to MyController->login()
$router->post("/login", ["controller" => "MyApp\\MyController@login"]);
// This will route a DELETE request to "/users/me" to MyController->deleteUser()
$router->delete("/users/me", ["controller" => "MyApp\\MyController@deleteUser"]);
// This will route a PUT request to "/users/profile/image" to MyController->uploadProfileImage()
$router->put("/users/profile/image", ["controller" => "MyApp\\MyController@uploadProfileImage"]);
```

The router takes advantage of the [Dependency Injection Container](/application/rdev/models/ioc) to instantiate your controller.

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
use RDev\Controllers;

class UserController extends Controllers\Controller
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

## Filters
Some routes might require actions to occur before and after the controller is called.  For example, you might want to check if a user is authenticated before allowing him or her access to a certain page.  This is when filters come in handy.  "Pre" filters are executed before the controller is called, and "post" filters are called after the controller.  Here is the order of precedence of return values of filters and controllers:

1. If a pre-filter returns something other than null, it is returned by the router, and the controller is never called
2. If the controller method returns something other than null, it is returned, and the post-filters are never called
3. If a post-filter returns something other than null, it is returned by the router

Filters are specified in the route options, and they must be registered to the router:
```php
$router->registerFilter("authenticate", function()
{
    if(!MyApp::isUserLoggedIn())
    {
        return new RDev\Models\HTTP\RedirectResponse("/login");
    }
});
$router->post("/users/posts", [
    "controller" => "MyApp\\UserController@createPost",
    "pre" => "authenticate" // Could also be an array of pre-filters
]);
```

Now, the "authenticate" filter will be called before the "createPost" method is called.  If the user is not logged in, he'll be redirected to the login page.  To apply "post" filters to a route, just add a "post" entry in the route options.

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
    $router->get("/users", ["controller" => "UserController"@showAllUsers"]);
    $router->get("/posts", ["controller" => "PostController"@showAllPosts"]);
});
```

Now, a GET request to "/users" will route to `MyApp\Controllers\UserController::showAllUsers()`, and a GET request to "/posts" will route to `MyApp\Controllers\PostController::showAllPosts()`.

#### Group Filters
Route groups allow you to apply "pre" and "post" filters to multiple routes:
```php
$router->group(["pre" => "authenticate"], function() use ($router)
{
    $router->get("/users/{userId}/profile", ["controller" => "MyApp\\UserController@showProfile"]);
    $router->get("/posts", ["controller" => "MyApp\\PostController@showPosts"]);
});
```

The "authenticate" filter will be executed on any matched routes inside the closure.

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