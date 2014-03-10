<?php
require __DIR__.'/../../vendor/autoload.php';
use SlimFacades\Facade;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Initialize Slim application
 */

$app = new Slim\Slim(array(
    'view'              => new \Slim\Views\Twig(),
    'templates.path'    => __DIR__.'/../views'
));

$view = $app->view();
$view->parserOptions = array(
    'debug' => true,
    'cache' => __DIR__ . '/../storage/cache'
);

$view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
);


$app->add(new \Slim\Middleware\SessionCookie(array(
    'expires' => '20 minutes',
    'path' => '/',
    'domain' => null,
    'secure' => false,
    'httponly' => false,
    'name' => 'slim_session',
    'secret' => 'Jeyac1uX54n',
    'cipher' => MCRYPT_RIJNDAEL_256,
    'cipher_mode' => MCRYPT_MODE_CBC
)));

// initialize the Slim Facade class

Facade::setFacadeApplication($app);
Facade::registerAliases(array(
    'Model'     => 'Illuminate\Database\Eloquent\Model',
    'Sentry'    => 'Cartalyst\Sentry\Facades\Native\Sentry',
    'App'       => 'SlimFacades\App',
    'Config'    => 'SlimFacades\Config',
    'Input'     => 'SlimFacades\Input',
    'Log'       => 'SlimFacades\Log',
    'Request'   => 'SlimFacades\Request',
    'Response'  => 'SlimFacades\Response',
    'Route'     => 'SlimFacades\Route',
    'View'      => 'SlimFacades\View',
));


/**
 * Boot up Eloquent
 */
require __DIR__.'/../config/database.php';

$capsule = new Capsule;
$capsule->addConnection(Config::get('database'));
$capsule->setAsGlobal();
$capsule->bootEloquent();

Sentry::setupDatabaseResolver($capsule->connection()->getPdo());
/**
 * Start the route
 */
require __DIR__.'/../routes.php';

return $app;