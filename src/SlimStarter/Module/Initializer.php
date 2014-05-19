<?php

namespace SlimStarter\Module;

abstract class Initializer implements ModuleInterface
{
    abstract function getModuleName();
    abstract function getModuleAccessor();

    public function registerAdminRoute()
    {

    }

    public function registerAdminMenu()
    {

    }

    public function registerPublicRoute()
    {

    }

    public function getTemplatePath()
    {
        return array(
            $this->getModuleAccessor() => 'views'
        );
    }

    public function registerHook()
    {

    }

    public function boot()
    {
        $this->registerHook();
    }

    public function install()
    {

    }

    public function uninstall()
    {

    }

    public function activate()
    {

    }

    public function deactivate()
    {

    }

}