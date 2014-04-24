<?php
namespace Admin;

class BaseController extends \BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->data['menu_active']  = 'dashboard';
        $this->data['menu_pointer'] = '<div class="pointer"><div class="arrow"></div><div class="arrow_border"></div></div>';
    }
}