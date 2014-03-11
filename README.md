SlimStarter
===========

SlimStarter is combination of simple but powerfull Slim Router, easy to use Eloquent ORM, Twig templating engine, 
and additional Sentry package.

With Slim Facade to provide simple access to Slim API

####Installation
Just clone or download zip from this repository, and run composer update

```
$git clone https://github.com/ikhsan017/SlimStarter.git
```

```
composer update
```

You need to import sql file in case you want to use Sentry, the file is located in

    vendor/cartalyst/sentry/schema/mysql.sql

to disable Sentry, simply remove it from composer.json, app/alias.php, app/bootstrap/start.php


####Configuration
all configuration should be placed in app/config directory

####Routing
All route configuration should be placed inside app/routes.php

file : app/routes.php

Route to closure
```php
Route::get('/', function(){
    App::render('welcome.twig');
});
```

Route to controller method
```php
/** get method */
Route::get('/', 'SomeController:someMethod');

/** post method */
Route::post('/post', 'PostController:create');

/** put method */
Route::put('/post/:id', 'PostController:update');

/** delete method */
Route::delete('/post/:id', 'PostController:destroy');
```

Route Middleware
```php
/** route middleware */
Route::get('/admin', function(){
    //check user login or redirect
}, 'AdminController:index');
```

Route group
```php
/** Route group */
Route::group('/book', function(){
    /** GET /book/ */
    Route::get('/', 'BookController:index');

    /** GET /book/:id */
    Route::get('/:id', 'BookController:show');

    /** GET /book/:id/edit */
    Route::get('/:id/edit', 'BookController:edit');

    /** PUT /book/:id */
    Route::put('/:id', 'BookController:update');

    /** DELETE /book/:id */
    Route::delete('/:id', 'BookController:destroy');

});
```

####Model
All models should be placed in app/models directory, since Eloquent is used as database provider, 
you can write model like you write model for Laravel

file : app/models/Book.php
```php
class Book Extends Model{}
```

####Controller
All controllers should be placed in app/controllers directory, you may extends the BaseController to get access to predefined helper

Slim instance available as $this->app

file : app/controllers/HomeController.php
```php
Class HomeController extends BaseController{

    public function welcome(){
        $this->data['title'] = 'Some title';
        App::render('welcome.twig', $this->data);
    }
}
```

####View
All view files should be placed in app/views in twig format

file : app/views/layout.twig
```html
<html>
    <head>
        <title>{{ title }}</title>
    </head>
    <body>
        {% block body %}

        {% endblock %}
    </body>
</html>
```

file : app/views/welcome.twig
```html
{% extends 'layout.twig' %}
{% block body %}
    Welcome to SlimStarter
{% endblock %}

```

rendering view inside controller
```php
App::render('welcome.twig', array('title' => 'Welcome to SlimStarter'));
```

####Hooks and Middlewares
    * All hooks and middlewares should be called within app/bootstrap/app.php
    * All middlewares class should be placed inside app/middlewares
    * Slim instance available as $app

file : app/bootstrap/app.php
```php
$app->hook('slim.before.route', function(){
    //do your hook
});

$app->add(new SomeActionMiddleware());
```

file : app/middlewares/SomeActionMiddleware.php
```php
class SomeActionMiddleware extends Middleware
{
    public function call()
    {
        // Get reference to application
        $app = $this->app;

        // Run inner middleware and application
        $this->next->call();

        // do your stuff
    }
}
```