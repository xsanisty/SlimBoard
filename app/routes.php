<?php

/**
 * Sample group routing with user check in middleware
 */
Route::group(
    '/admin', 
    function() use ($app){
        if(!Sentry::check()){
            Response::redirect($app->urlFor('login'));
        }
    },
    function(){
        /**
         * sample namespaced controller
         */
        Route::get('/', 'Admin\AdminController:index')->name('admin');
    }
);

Route::get('/login', 'Admin\AdminController:login')->name('login');
Route::post('/login', 'Admin\AdminController:doLogin');

/**
 * default routing
 */
Route::get('/', function() use ($app){
    $app->render('welcome.twig', array('title' => 'Hello world!'));
});