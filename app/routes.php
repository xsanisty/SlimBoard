<?php

/**
 * Sample group routing with user check in middleware
 */
Route::group(
    '/admin',
    function(){
        if(!Sentry::check()){

            if(Request::isAjax()){
                Response::headers()->set('Content-Type', 'application/json');
                Response::setBody(json_encode(
                    array(
                        'success'   => false,
                        'message'   => 'Session expired or unauthorized access.',
                        'code'      => 401
                    )
                ));
                App::stop();
            }else{
                $redirect = Request::getResourceUri();
                Response::redirect(App::urlFor('login').'?redirect='.base64_encode($redirect));
            }
        }
    },
    function(){
        /** sample namespaced controller */
        Route::get('/', 'Admin\AdminController:index')->name('admin');

        /** grouping user inside admin group to indicate resource */
        Route::group('/user', function(){
            Route::get('/', 'Admin\UserController:index');
            Route::get('/page/:page', 'Admin\UserController:index');
            Route::post('/', 'Admin\UserController:store');
            Route::get('/:id', 'Admin\UserController:show');
            Route::put('/:id', 'Admin\UserController:update');
            Route::get('/:id/edit', 'Admin\UserController:edit');
            Route::delete('/:id', 'Admin\UserController:destroy');
        });
    }
);

Route::get('/login', 'Admin\AdminController:login')->name('login');
Route::get('/logout', 'Admin\AdminController:logout')->name('logout');
Route::post('/login', 'Admin\AdminController:doLogin');

/** Route to documentation */
Route::get('/doc(/:page+)', 'DocController:index');

/** default routing */
Route::get('/', 'HomeController:welcome');