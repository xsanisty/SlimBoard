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

foreach (glob(APP_PATH.'config/*.php') as $configFile) {
    require $configFile;
}


/**
 * Initialize Slim application
 */
$app = new Slim\Slim($config['app']);

$app->add(new \Slim\Middleware\SessionCookie($config['cookie']));

$view = $app->view();
$view->parserOptions = $config['twig'];

$view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
);

/**
 * initialize the Slim Facade class
 */
Facade::setFacadeApplication($app);
Facade::registerAliases($config['alias']);


/**
 * Boot up Eloquent
 */
$capsule = new Capsule;
$capsule->addConnection($config['database']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

/**
 * Setting up Sentry
 */
Sentry::setupDatabaseResolver($capsule->connection()->getPdo());

/**
 * Setting up Slim hooks and middleware
 */
require APP_PATH.'bootstrap/app.php';

/**
 * Start the route
 */
require APP_PATH.'routes.php';

return $app;