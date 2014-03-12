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
        App::render('admin/index.twig', $this->data);
    }

    /**
     * display the login form
     */
    public function login(){
        if(Sentry::check()){
            Response::redirect($this->siteUrl('admin'));
        }else{
            App::render('admin/login.twig', $this->data);
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

            $remember = Input::post('remember', false);

            // Try to authenticate the user
            $user = Sentry::authenticate($credential, false);

            if($remember){
                Sentry::loginAndRemember($user);
            }else{
                Sentry::login($user, false);
            }

            Response::redirect($this->siteUrl('admin'));
        }catch(\Exception $e){
            App::flash('message', $e->getMessage());

            Response::redirect($this->siteUrl('login'));
        }
    }

    /**
     * Logout the user
     */
    public function logout(){
        Sentry::logout();

        Response::redirect($this->siteUrl('login'));
    }

}