<?php

session_cache_limiter(false);
session_start();

define('VENDOR_PATH', __DIR__.'/../../vendor/');
define('APP_PATH', __DIR__.'/../../app/');
define('PUBLIC_PATH', __DIR__.'/../../public/');

require VENDOR_PATH.'autoload.php';

/**
 * Load the configuration
 */
$config = array();

foreach (glob(APP_PATH.'config/*.php') as $configFile) {
    require $configFile;
}

/** Merge cookies config to slim config */
if(isset($config['cookies'])){
    foreach($config['cookies'] as $configKey => $configVal){
        $config['slim']['cookies.'.$configKey] = $configVal;
    }
}

/**
 * Initialize Slim application
 */
$app = new \Slim\Slim($config['slim']);

$app->view->parserOptions = $config['twig'];
$app->view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
);

/**
 * Initialize the Slim Facade class
 */
\SlimFacades\Facade::setFacadeApplication($app);
\SlimFacades\Facade::registerAliases($config['aliases']);

/**
 * Publish the configuration to Slim instance so controller have access to it via
 */
foreach ($config as $configKey => $configVal) {
    if($configKey != 'slim'){
        $app->config($configKey, $configVal);

        if($configKey != 'cookies'){
            foreach($configVal as $subConfigKey => $subConfigVal){
                $app->config($configKey.'.'.$subConfigKey, $subConfigVal);
            }
        }
    }
    
}

/**
 * if called from the install script, disable all hooks, middlewares, and database init
 */
if(!defined('INSTALL')){

    /**
     * Setting up Slim hooks and middleware
     */
    require APP_PATH.'bootstrap/app.php';

    /**
     * Start the route
     */
    require APP_PATH.'routes.php';
}

return $app;