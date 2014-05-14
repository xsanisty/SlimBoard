<?php

/**
 * Hook, filter, etc should goes here
 */

/**
 * error handling sample
 *
 * $app->error(function() use ($app){
 *     $app->render('error.html');
 * });
 */


/**
 * Initialize SlimStarter, boot up Eloquent and boot up modules
 */

use Illuminate\Database\Capsule\Manager as DatabaseManager;
use SlimStarter\Module\Manager as ModuleManager;
use SlimStarter\Helper\MenuManager;

$app->hook('initialize', function() use ($app, $config){
    /**
     * Boot up Eloquent
     */
    try{
        $app->container->singleton('db',function(){
            return new DatabaseManager;
        });

        $app->db->addConnection($config['database']['connections'][$config['database']['default']]);
        $app->db->setAsGlobal();
        $app->db->bootEloquent();

        /**
         * Setting up Sentry
         */
        Sentry::setupDatabaseResolver($app->db->connection()->getPdo());
    }catch(PDOException $e){
        if(file_exists(PUBLIC_PATH.'install.php') && !defined('INSTALL')){
            /**
             * In case app can not connect to the database and install script was found, redirect to install script
             * we assume that application is not configured yet
             */

            $publicPath  = dirname($_SERVER['SCRIPT_NAME']).'/';
            $installPath = Request::getUrl().$publicPath.'install.php';
            Response::redirect($installPath);
        }else{
            /**
             * If install script can not be found, re throw the exception to be handled by Slim
             */

            throw $e;
        }
    }


    /**
     * Boot up module manager and load all modules
     */
    $app->container->singleton('module', function() use ($app){
        return new ModuleManager($app);
    });

    foreach (glob(APP_PATH.'modules/*') as $module) {
        $className = basename($module);
        $moduleBootstrap = "\\$className\\Initialize";

        $app->module->register(new $moduleBootstrap);
    }

    $app->module->boot();
});

$app->applyHook('initialize');