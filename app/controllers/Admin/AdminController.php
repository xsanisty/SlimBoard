<?php

namespace Admin;

use \App;
use \View;
use \Input;
use \Sentry;
use \Response;

class AdminController extends \BaseController{

    /**
     * display the admin dashboard
     */
    public function index(){
        App::render('admin/index.twig');
    }

    /**
     * display the login form
     */
    public function login(){
        if(!Sentry::check()){
            App::render('admin/login.twig', $this->data);
        }else{
            Response::redirect(App::urlFor('admin'));
        }
    }

    /**
     * Process the login
     */
    public function doLogin(){
        
        try
        {
            $credential = array(
                'email'     => Input::post('email'),
                'password'  => Input::post('password')
            );

            // Try to authenticate the user
            $user = Sentry::authenticate($credential, false);

            Sentry::login($user, false);

            Response::redirect(App::urlFor('admin'));
        }catch(\Exception $e){
            App::flash('message', $e->getMessage());

            Response::redirect(App::urlFor('login'));
        }
    }

    /**
     * Logout the user
     */
    public function logout(){
        Sentry::logout();

        Response::redirect(App::urlFor('login'));
    }

}