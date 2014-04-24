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
 * Boot up Eloquent
 */

use Illuminate\Database\Capsule\Manager as Capsule;
$app->hook('slim.before', function() use ($app, $config){
    try{
        $app->container->singleton('db',function(){
            return new Capsule;;
        });

        $app->db->addConnection($config['database']);
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
});