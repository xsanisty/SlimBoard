<?php

namespace SlimStarter;

use \Illuminate\Database\Capsule\Manager as DatabaseManager;
use \SlimStarter\Module\Manager as ModuleManager;
use \SlimStarter\Helper\MenuManager;
use \SlimFacades\Facade;
/**
 * SlimStarter Bootstrapper, initialize all the thing needed on the start
 */
class Bootstrap
{
    protected $app;
    protected $config;

    /**
     * SlimStarter Bootstrap constructor
     * @param \Slim\Slim $app
     */
    public function __construct(\Slim\Slim $app = null){
        $this->app = $app;
    }

    /**
     * Setup SlimStarter configuration and inject it to Slim instance
     * @param array $config
     */
    public function setConfig($config){
        $this->config = $config;
        foreach ($config as $key => $value) {
            $this->app->config($key, $value);
        }
    }

    /**
     * Setting up slim instance for slim starter
     * @param SlimSlim $app [description]
     */
    public function setApp(\Slim\Slim $app){
        $this->app = $app;
    }

    /**
     * Boot up Slim Facade accessor
     * @param  Array $config
     */
    public function bootFacade($config){
        Facade::setFacadeApplication($this->app);
        Facade::registerAliases($config);
    }

    /**
     * Boot up Eloquent ORM and inject to Slim container
     * @param  Array $config
     */
    public function bootEloquent($config){
        try{
            $this->app->container->singleton('db',function(){
                return new DatabaseManager;
            });

            $this->app->db->addConnection($config['connections'][$config['default']]);
            $this->app->db->setAsGlobal();
            $this->app->db->bootEloquent();
        }catch(\Exception $e){
            throw $e;
        }
    }

    /**
     * Boot up module manager and inject to Slim container
     * @return [type] [description]
     */
    public function bootModuleManager(){
        $app = $this->app;
        $this->app->container->singleton('module', function() use ($app){
            return new ModuleManager($app);
        });
    }

    /**
     * Boot up Sentry authorization provider
     * @param  Array $config
     */
    public function bootSentry($config){
        \Sentry::setupDatabaseResolver($this->app->db->connection()->getPdo());
    }

    /**
     * Boot up Twig template engine
     * @param  Array $config
     */
    public function bootTwig($config){
        $this->app->view->parserOptions = $config;
        $this->app->view->parserExtensions = array(
            new \Slim\Views\TwigExtension(),
        );
    }

    /**
     * Run the boot sequence
     * @return [type] [description]
     */
    public function boot(){
        $this->bootFacade($this->config['aliases']);
        $this->bootEloquent($this->config['database']);
        $this->bootModuleManager();
        $this->bootTwig($this->config['twig']);
        $this->bootSentry($this->config['sentry']);
    }

    /**
     * Run the Slim application
     */
    public function run(){
        $this->app->run();
    }
}