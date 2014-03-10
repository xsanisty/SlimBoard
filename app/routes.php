<?php

/**
 * Sample group routing with user check in middleware
 */
Route::group(
    '/admin', 
    function(){
        if(!Sentry::check()){
            Response::redirect(App::urlFor('login'));
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
Route::get('/logout', 'Admin\AdminController:logout')->name('logout');
Route::post('/login', 'Admin\AdminController:doLogin');

/**
 * default routing
 */
Route::get('/', 'HomeController:welcome');