<?php
namespace SlimStarter\Module;

interface ModuleInterface{
    public function getModuleName();
    public function getModuleAccessor();
    public function getTemplatePath();
    public function registerAdminRoute();
    public function registerAdminMenu();
    public function registerPublicRoute();
    public function registerHook();
    public function boot();
    public function install();
    public function uninstall();
    public function activate();
    public function deactivate();
}