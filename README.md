SlimStarter
===========

SlimStarter is a bootstrap application built with Slim Framework in MVC architecture,
with Laravel's Eloquent as database provider (Model) and Twig as template engine (View).

Additional package is Sentry as authentication provider and Slim-facade which provide easy access to underlying Slim API
with static interface like Laravel syntax (built based on Laravel's Facade).

####Showcase
You can test SlimStarter in live site by visiting here :
(shared hosting) http://slimstarter.xsanisty.com
(pagodabox) http://slimstarter.gopagoda.com

with username ```admin@admin.com``` and password ```password```.


####Installation

> You can now install SlimStarter on pagodabox via App Cafe https://pagodabox.com/cafe/ikhsan017/slimstarter


#####1 Manual Install
You can manually install SlimStarter by cloning this repo or download the zip file from this repo, and run ```composer install```.
```
$git clone https://github.com/xsanisty/SlimStarter.git .
$composer install
```

#####2 Install via ```composer create-project```
Alternatively, you can use ```composer create-project``` to install SlimStarter without downloading zip or cloning this repo.

```
composer create-project xsanisty/slim-starter --stability="dev"
```

#####3 Setup Permission
After composer finished install the dependencies, you need to change file and folder permission.
```
chmod -R 777 app/storage/
chmod 666 app/config/database.php
```

#####4 Configure and Setup Database
You can now access the installer by pointing install.php in your browser
```
http://localhost/path/to/SlimStarter/public/install.php
```



####Configuration
Configuration file of SlimStarter located in ```app/config```, edit the database.php, cookie.php and other to match your need

####Routing
Routing configuration is located in ```app/routes.php```, it use Route facade to access underlying Slim router.
If you prefer the 'Slim' way, you can use $app to access Slim instance


Route to closure
```php
Route::get('/', function(){
    View::display('welcome.twig');
});

/** the Slim way */
$app->get('/', function() use ($app){
    $app->view->display('welcome.twig');
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
    //route middleware to check user login or redirect
}, 'AdminController:index');
```

Route group
```php
/** Route group to book resource */
Route::group('/book', function(){
    Route::get('/', 'BookController:index'); // GET /book
    Route::post('/', 'BookController:store'); // POST /book
    Route::get('/create', 'BookController:create'); // Create form of /book
    Route::get('/:id', 'BookController:show'); // GET /book/:id
    Route::get('/:id/edit', 'BookController:edit'); // GET /book/:id/edit
    Route::put('/:id', 'BookController:update'); // PUT /book/:id
    Route::delete('/:id', 'BookController:destroy'); // DELETE /book/:id
});
```

Route Resource
this will have same effect on route group above like Laravel Route::resource
```php
/** Route to book resource */
Route::resource('/book', 'BookController');
```

RouteController
```php
/** Route to book resource */
Route::controller('/book', 'BookController');

/**
 * GET /book will be mapped to BookController:getIndex
 * POST /book will be mapped to BookController:postIndex
 * [METHOD] /book/[path] will be mapped to BookController:methodPath
 */
```

####Model
Models are located in ```app/models``` directory, since Eloquent is used as database provider, you can write model like you
write model for Laravel, for complete documentation about eloquent, please refer to http://laravel.com/docs/eloquent

file : app/models/Book.php
```php
class Book Extends Model{}
```
>Note: Eloquent has some limitations due to dependency to some Laravel's and Symfony's components which is not included,
such as ```remember()```, ```paginate```, and validation method, which is depend on ```Illuminate\Cache```, ```Illuminate\Filesystem```,
```Symfony\Finder```, etc.

####Controller
Controllers are located in ```app/controllers``` directory, you may extends the BaseController to get access to predefined helper.
You can also place your controller in namespace to group your controller.

file : app/controllers/HomeController.php
```php
Class HomeController extends BaseController{

    public function welcome(){
        $this->data['title'] = 'Some title';
        View::display('welcome.twig', $this->data);
    }
}
```

#####Controller helper

######Get reference to Slim instance
You can access Slim instance inside your controller by accessing $app property
```php
$this->app; //reference to Slim instance
```

######Loading javascript assets or CSS assets
SlimStarter shipped with default master template with js and css asset already in place, to load your own js or css file
you can use ```loadJs``` or ```loadCss``` , ```removeJs``` or ```removeCss``` to remove js or css, ```resetJs``` or ```resetCss```
to remove all queued js or css file.

```php
/**
 * load local js file located in public/assets/js/application.js
 * by default, it will be placed in the last list,
 * to modify it, use position option in second parameter
 * array(
 *      'position' => 'last|first|after:file|before:file'
 * )
 */
$this->loadJs('application.js', ['position' => 'after:jquery.js'])

/**
 * load external js file, eg: js in CDN
 * use location option in second parameter
 * array(
 *      'location' => 'internal|external'
 * )
 */
$this->loadJs('http://code.jquery.com/jquery-1.11.0.min.js', ['location' => 'external']);

/** remove js file from the list */
$this->removeJs('user.js');

/** reset js queue, no js file will be loaded */
$this->resetJs();


/** load local css file located in public/assets/css/style.css */
$this->loadCss('style.css')

/** load external css file, eg: js in CDN */
$this->loadCss('//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css', ['location' => 'external']);

/**
```

######Publish PHP variable to javascript
You can also publish PHP variable to make it accessible via javascript (must extends master.twig)
```php
/** publish the variable */
$this->publish('user', User::find(1)->toArray());

/** remove the variable */
$this->unpublish('user');
```

the user variable will be accessible in 'global' namespace
```javascript
console.log(global.user);
```

######Default variable available in template

####View
Views file are located in ```app/views``` directory in twig format, there is master.twig with 'body' block as default master template
shipped with SlimStarer that will provide default access to published js variable.

For detailed Twig documentation, please refer to http://twig.sensiolabs.org/documentation


file : app/views/welcome.twig
```html
{% extends 'master.twig' %}
{% block body %}
    Welcome to SlimStarter
{% endblock %}

```

#####Rendering view inside controller
If your controller extends the BaseController class, you will have access to $data property which will be the placeholder for all
view's data.

```php
View::display('welcome.twig', $this->data);
```

####Hooks and Middlewares
You can still hook the Slim event, or registering Middleware to Slim instance in ```app/bootstrap/app.php```,
Slim instance is accessible in ```$app``` variable.

```php
$app->hook('slim.before.route', function(){
    //do your hook
});

$app->add(new SomeActionMiddleware());
```

You can write your own middleware class in ```app/middlewares``` directory.

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

In case autoloader cannot resolve your classes, do ```composer dump-autoload``` so composer can resolve your class location
