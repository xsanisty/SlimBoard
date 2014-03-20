<?php

namespace Admin;

use \App;
use \View;
use \Input;
use \Sentry;
use \Response;

class AdminController extends BaseController
{

    /**
     * display the admin dashboard
     */
    public function index()
    {
        View::display('admin/index.twig', $this->data);
    }

    /**
     * display the login form
     */
    public function login()
    {
        if(Sentry::check()){
            Response::redirect($this->siteUrl('admin'));
        }else{
            $this->data['redirect'] = (Input::get('redirect')) ? base64_decode(Input::get('redirect')) : '';
            View::display('admin/login.twig', $this->data);
        }
    }

    /**
     * Process the login
     */
    public function doLogin()
    {
        $remember = Input::post('remember', false);
        $email    = Input::post('email');
        $redirect = Input::post('redirect');
        $redirect = ($redirect) ? $redirect : 'admin';

        try{
            $credential = array(
                'email'     => $email,
                'password'  => Input::post('password')
            );

            // Try to authenticate the user
            $user = Sentry::authenticate($credential, false);

            if($remember){
                Sentry::loginAndRemember($user);
            }else{
                Sentry::login($user, false);
            }

            Response::redirect($this->siteUrl($redirect));
        }catch(\Exception $e){
            App::flash('message', $e->getMessage());
            App::flash('email', $email);
            App::flash('redirect', $redirect);
            App::flash('remember', $remember);

            Response::redirect($this->siteUrl('login'));
        }
    }

    /**
     * Logout the user
     */
    public function logout()
    {
        Sentry::logout();

        Response::redirect($this->siteUrl('login'));
    }

}