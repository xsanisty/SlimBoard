SlimStarter
===========

SlimStarter is combination of simple but powerfull Slim Router, easy to use Eloquent ORM, Twig templating engine, 
and additional Sentry package.

With Slim Facade to provide simple access to Slim API

*** Installation ***
Just clone or download zip from this repository, and run composer update

```
$git clone https://github.com/ikhsan017/SlimStarter.git
```

```
composer update
```

*** Configuration ***
all configuration should be placed in app/config directory

*** Model ***
All models should be placed in app/models directory, since Eloquent is used as database provider, 
you can write model like you write model for Laravel

file : app/models/Book.php
```php
class Book Extends Model{}
```

*** Controller ***
All controllers should be placed in app/controllers directory, you may extends the BaseController to get access to predefined helper

file : app/controllers/HomeController.php
```php
Class HomeController extends BaseController{

    public function welcome(){
        $this->data['title'] = 'Some title';
        App::render('welcome.twig', $this->data);
    }
}
```

*** View ***
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

*** Hooks and Middlewares ***
All hooks and middlewares should be called within app/bootstrap/app.php
All middlewares class should be placed inside app/middlewares