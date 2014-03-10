<?php

namespace Admin;

use \View;

class AdminController{

    /**
     * display the admin dashboard
     */
    public function index(){
        View::display('admin/index.twig');
    }

    /**
     * display the login form
     */
    public function login(){
        View::display('admin/login.twig');
    }

    /**
     * Process the login
     */
    public function doLogin(){
        echo 'I am here';
    }

}