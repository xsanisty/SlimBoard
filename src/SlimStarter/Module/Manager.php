<?php

namespace SlimStarter\Module;

use \Twig_Loader_Filesystem;

class Manager{
    private $modules;
    private $app;

    public function __construct(\Slim\Slim $app = null){
        $this->modules = array();
        $this->app = $app;
    }

    public function setApp(\Slim\Slim $app){
        $this->app = $app;
    }

    public function run($module){

    }

    public function trigger($event){

    }

    public function install($module){

    }

    public function uninstall($module){

    }

    public function register(ModuleInterface $module){
        $this->modules[$module->getModuleAccessor()] = $module;
    }

    public function boot(){

        $twigInstance   = $this->app->view->getEnvironment();
        $twigLoader     = new Twig_Loader_Filesystem();

        foreach ($this->modules as $module) {
            $prefixDir = $module->getModuleName();
            $modulePath= $this->app->config('path.module').$prefixDir.'/';

            /** registering module view namespace */
            foreach ($module->getTemplatePath() as $namespace => $dir) {
                $moduleTemplatePath = $modulePath.$dir;
                $twigLoader->addPath($moduleTemplatePath, $namespace);
            }

            $module->boot();
        }

        $twigLoader->addPath($this->app->config('templates.path'));


        $twigInstance->setLoader($twigLoader);
    }

    public function getModules(){
        return $this->modules;
    }
}