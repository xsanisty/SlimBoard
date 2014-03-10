<?php

define('VENDOR_PATH', __DIR__.'/../../vendor/');
define('APP_PATH', __DIR__.'/../../app/');

require VENDOR_PATH.'autoload.php';
use SlimFacades\Facade;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Load the configuration
 */
$config = array();

foreach (glob(APP_PATH.'config/*.php') as $filename) {
    require $filename;
}


/**
 * Initialize Slim application
 */
$app = new Slim\Slim(array(
    'view'              => new \Slim\Views\Twig(),
    'templates.path'    => APP_PATH.'views'
));

$app->add(new \Slim\Middleware\SessionCookie($config['cookie']));

$view = $app->view();
$view->parserOptions = $config['twig'];

$view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
);

// initialize the Slim Facade class
Facade::setFacadeApplication($app);
Facade::registerAliases($config['alias']);


/**
 * Boot up Eloquent
 */
$capsule = new Capsule;
$capsule->addConnection($config['database']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

Sentry::setupDatabaseResolver($capsule->connection()->getPdo());
/**
 * Start the route
 */
require APP_PATH.'routes.php';

return $app;