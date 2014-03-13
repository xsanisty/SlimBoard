<?php

namespace Admin;

use \App;
use \User;
use \Sentry;
use \Request;
use \Response;
use \Input;

class UserController extends \BaseController{

    /**
     * display list of resource
     */
    public function index($page = 1){
        $user = Sentry::getUser();
        $this->data['title'] = 'Users List';
        $this->data['users'] = User::where('id', '<>', $user->id)->get()->toArray();

        /** load angular.js library */
        $this->loadJs('https://ajax.googleapis.com/ajax/libs/angularjs/1.3.0-beta.1/angular.min.js', array('location' => 'external'));
        $this->loadJs('https://ajax.googleapis.com/ajax/libs/angularjs/1.3.0-beta.1/angular-resource.min.js', array('location' => 'external'));
        
        /** load the user.js app */
        $this->loadJs('app/user.js');

        /** publish necessary js  variable */
        $this->publish('baseUrl', $this->data['baseUrl']);

        /** render the template */
        App::render('admin/user/index.twig', $this->data);
    }

    /**
     * display resource with specific id
     */
    public function show($id){
        if(Request::isAjax()){
            $user = null;
            $message = '';

            try{
                $user = Sentry::findUserById($id);
            }catch(\Exception $e){
                $message = $e->getMessage();
            }


            Response::headers()->set('Content-Type', 'application/json');
            Response::setBody(json_encode(
                array(
                    'success'   => !is_null($user),
                    'data'      => !is_null($user) ? $user->toArray() : false,
                    'message'   => $message
                )
            ));
        }else{

        }
    }

    /**
     * show edit from resource with specific id
     */
    public function edit($id){
        echo 'edit user with id '.$id;
    }

    /**
     * update resource with specific id
     */
    public function update($id){
        $success = false;
        $message = '';
        $user = null;
        try
        {
            $input = Input::put();

            if($input['password'] != $input['confirm_password']){
                throw new Exception("Password and confirmation password not match", 1);
            }

            $user = Sentry::findUserById($id);

            $user->email = $input['email'];
            $user->first_name = $input['first_name'];
            $user->last_name = $input['last_name'];

            if($input['password']){
                $user->password = $input['password'];
            }

            $success = $user->save();
        }
        catch (\Exception $e)
        {
            $success = false;
            $message = $e->getMessage();
        }

        if(Request::isAjax()){
            Response::headers()->set('Content-Type', 'application/json');
            Response::setBody(json_encode(
                array(
                    'success'   => $success,
                    'data'      => ($user) ? $user->toArray() : null,
                    'message'   => $message
                )
            ));
        }else{
            Response::redirect($this->siteUrl('admin/user/'.$id.'/edit'));
        }
    }

    /**
     * create new resource
     */
    public function store(){
        $user = null;
        $message = '';

        try{
            $input = Input::post();

            if($input['password'] != $input['confirm_password']){
                throw new \Exception("Password and confirmation password not match", 1);
            }

            $user = Sentry::createUser(array(
                'email'       => $input['email'],
                'password'    => $input['password'],
                'first_name'  => $input['first_name'],
                'last_name'   => $input['last_name'],
                'activated'   => 1
            ));

            $success = true;
            $message = 'User created successfully';
        }catch (\Exception $e){
            $message = $e->getMessage();
            $success = false;
        }

        if(Request::isAjax()){
            Response::headers()->set('Content-Type', 'application/json');
            Response::setBody(json_encode(
                array(
                    'success'   => $success,
                    'data'      => ($user) ? $user->toArray() : null,
                    'message'   => $message
                )
            ));
        }else{
            Response::redirect($this->siteUrl('admin/user'));
        }
    }

    /**
     * destroy resource with specific id
     */
    public function destroy($id){
        $id = (int) $id;

        $destroyed = User::destroy($id);

        if(Request::isAjax()){
            Response::headers()->set('Content-Type', 'application/json');
            Response::setBody(json_encode(
                array(
                    'success'   => $destroyed,
                    'id'        => $id
                )
            ));
        }else{
            Response::redirect($this->siteUrl('admin/user'));
        }
    }
}