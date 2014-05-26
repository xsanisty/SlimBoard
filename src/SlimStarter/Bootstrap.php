<?php

namespace SlimStarter;

use \Illuminate\Database\Capsule\Manager as DatabaseManager;
use \SlimStarter\Module\Manager as ModuleManager;
use \SlimStarter\Menu\MenuManager;
use \SlimFacades\Facade;
use \Cartalyst\Sentry\Cookies\NativeCookie;
use \Cartalyst\Sentry\Sessions\NativeSession;
use \Cartalyst\Sentry\Groups\Eloquent\Provider as GroupProvider;
use \Cartalyst\Sentry\Hashing\BcryptHasher;
use \Cartalyst\Sentry\Hashing\NativeHasher;
use \Cartalyst\Sentry\Hashing\Sha256Hasher;
use \Cartalyst\Sentry\Hashing\WhirlpoolHasher;
use \Cartalyst\Sentry\Sentry;
use \Cartalyst\Sentry\Throttling\Eloquent\Provider as ThrottleProvider;
use \Cartalyst\Sentry\Users\Eloquent\Provider as UserProvider;
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
        $app = $this->app;
        $this->app->container->singleton('sentry', function() use ($app, $config){

            $hasherProvider     = $this->hasherProviderFactory($config);
            $userProvider       = $this->userProviderFactory($hasherProvider, $config);
            $groupProvider      = $this->groupProviderFactory($config);
            $throttleProvider   = $this->throttleProviderFactory($userProvider, $config);

            return new Sentry(
                $userProvider,
                $groupProvider,
                $throttleProvider,
                new NativeSession,
                new NativeCookie,
                $app->request->getIp()
            );
        });
    }

    /** Sentry specific factory, adopted from SentryServiceProvider */
    protected function hasherProviderFactory($config){
        $hasher  = $config['hasher'];
        switch ($hasher)
        {
            case 'native':
                return new NativeHasher;
                break;

            case 'bcrypt':
                return new BcryptHasher;
                break;

            case 'sha256':
                return new Sha256Hasher;
                break;

            case 'whirlpool':
                return new WhirlpoolHasher;
                break;
        }

        throw new \InvalidArgumentException("Invalid hasher [$hasher] chosen for Sentry.");
    }

    protected function userProviderFactory($hasher, $config){
            $model = $config['users']['model'];

            if (method_exists($model, 'setLoginAttributeName'))
            {
                $loginAttribute = $config['users']['login_attribute'];

                forward_static_call_array(
                    array($model, 'setLoginAttributeName'),
                    array($loginAttribute)
                );
            }

            // Define the Group model to use for relationships.
            if (method_exists($model, 'setGroupModel'))
            {
                $groupModel = $config['groups']['model'];

                forward_static_call_array(
                    array($model, 'setGroupModel'),
                    array($groupModel)
                );
            }

            // Define the user group pivot table name to use for relationships.
            if (method_exists($model, 'setUserGroupsPivot'))
            {
                $pivotTable = $config['user_groups_pivot_table'];

                forward_static_call_array(
                    array($model, 'setUserGroupsPivot'),
                    array($pivotTable)
                );
            }

            return new UserProvider($hasher, $model);
    }

    protected function groupProviderFactory($config){
        $model = $config['groups']['model'];

            // Define the User model to use for relationships.
            if (method_exists($model, 'setUserModel'))
            {
                $userModel = $config['users']['model'];

                forward_static_call_array(
                    array($model, 'setUserModel'),
                    array($userModel)
                );
            }

            // Define the user group pivot table name to use for relationships.
            if (method_exists($model, 'setUserGroupsPivot'))
            {
                $pivotTable = $config['user_groups_pivot_table'];

                forward_static_call_array(
                    array($model, 'setUserGroupsPivot'),
                    array($pivotTable)
                );
            }

            return new GroupProvider($model);
    }

    protected function throttleProviderFactory($userProvider,$config){
        $model = $config['throttling']['model'];

        $throttleProvider = new ThrottleProvider($userProvider, $model);

            if ($config['throttling']['enabled'] === false)
            {
                $throttleProvider->disable();
            }

            if (method_exists($model, 'setAttemptLimit'))
            {
                $attemptLimit = $config['throttling']['attempt_limit'];

                forward_static_call_array(
                    array($model, 'setAttemptLimit'),
                    array($attemptLimit)
                );
            }
            if (method_exists($model, 'setSuspensionTime'))
            {
                $suspensionTime = $config['throttling']['suspension_time'];

                forward_static_call_array(
                    array($model, 'setSuspensionTime'),
                    array($suspensionTime)
                );
            }

            // Define the User model to use for relationships.
            if (method_exists($model, 'setUserModel'))
            {
                $userModel = $config['users']['model'];

                forward_static_call_array(
                    array($model, 'setUserModel'),
                    array($userModel)
                );
            }

            return $throttleProvider;
    }

    /**
     * Boot up Twig template engine
     * @param  Array $config
     */
    public function bootTwig($config){
        $app    = $this->app;
        $view   = $app->view;

        $view->parserOptions = $config;
        $view->parserExtensions = array(
            new \Slim\Views\TwigExtension(),
            new \SlimStarter\TwigExtension\MenuRenderer()
        );
    }

    /**
     * Boot up Menu Manager
     */
    public function bootMenuManager(){
        $this->app->container->singleton('menu', function(){
            return new MenuManager;
        });
    }

    /**
     * Run the boot sequence
     * @return [type] [description]
     */
    public function boot(){
        $this->bootFacade($this->config['aliases']);
        $this->bootMenuManager();
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