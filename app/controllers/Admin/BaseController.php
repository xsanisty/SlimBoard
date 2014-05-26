<?php
namespace Admin;

use \App;
use \Menu;
use \Module;

class BaseController extends \BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->data['menu_pointer'] = '<div class="pointer"><div class="arrow"></div><div class="arrow_border"></div></div>';

        $adminMenu = Menu::create('admin_sidebar');
        $dashboard = $adminMenu->createItem('dashboard', array(
            'label' => 'Dashboard',
            'icon'  => 'dashboard',
            'url'   => 'admin'
        ));

        $adminMenu->addItem('dashboard', $dashboard);
        $adminMenu->setActiveMenu('dashboard');

        foreach (Module::getModules() as $module) {
            $module->registerAdminMenu();
        }

    }
}