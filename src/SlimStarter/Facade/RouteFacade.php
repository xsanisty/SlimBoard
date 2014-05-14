<?php

namespace SlimStarter\Facade;

class RouteFacade extends \SlimFacades\Route{

    /**
     * Route resource to single controller
     */
    public static function resource(){
        $arguments  = func_get_args();
        $path       = $arguments[0];
        $controller = end($arguments);

        $resourceRoutes = array(
            'get'           => array(
                'pattern'       => "$path",
                'method'        => 'get',
                'handler'       => "$controller:index"
            ),
            'get_paginate'  => array(
                'pattern'       => "$path/page/:page",
                'method'        => 'get',
                'handler'       => "$controller:index"
            ),
            'get_create'    => array(
                'pattern'       => "$path/create",
                'method'        => 'get',
                'handler'       => "$controller:create"
            ),
            'get_edit'      => array(
                'pattern'       => "$path/:id/edit",
                'method'        => 'get',
                'handler'       => "$controller:edit"
            ),
            'get_show'      => array(
                'pattern'       => "$path/:id",
                'method'        => 'get',
                'handler'       => "$controller:show"
            ),
            'post'          => array(
                'pattern'       => "$path",
                'method'        => 'post',
                'handler'       => "$controller:store"
            ),
            'put'           => array(
                'pattern'       => "$path/:id",
                'method'        => 'put',
                'handler'       => "$controller:update"
            ),
            'delete'        => array(
                'pattern'       => "$path/:id",
                'method'        => 'delete',
                'handler'       => "$controller:destroy"
            )
        );

        foreach ($resourceRoutes as $route) {
            $callable   = $arguments;

            //put edited pattern to the top stack
            array_shift($callable);
            array_unshift($callable, $route['pattern']);

            //put edited controller to the bottom stack
            array_pop($callable);
            array_push($callable, $route['handler']);

            call_user_func_array(array(self::$slim, $route['method']), $callable);
        }
    }

    /**
     * Map route to all public controller method
     *
     * with
     * Route::get('/prefix', 'ClassController')
     *
     * this will map
     * GET  domain.com/prefix -> ClassController::getIndex
     * POST domain.com/prefix -> ClassCOntroller::postIndex
     * PUT  domain.com/prefix -> ClassCOntroller::putIndex
     */
    public static function controller(){

        $arguments  = func_get_args();
        $path       = $arguments[0];
        $controller = end($arguments);

        $class      = new \ReflectionClass($controller);
        $controllerMethods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);

        $uppercase  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        foreach ($controllerMethods as $method) {
            if(substr($method->name, 0, 2) != '__'){
                $methodName = $method->name;
                $callable   = $arguments;

                $pos        = strcspn($methodName, $uppercase);
                $httpMethod = substr($methodName, 0, $pos);
                $ctrlMethod = lcfirst(strpbrk($methodName, $uppercase));

                if($ctrlMethod == 'index'){
                    $pathMethod = $path;
                }else if($httpMethod == 'get'){
                    $pathMethod = "$path/$ctrlMethod(/:params+)";
                }else{
                    $pathMethod = "$path/$ctrlMethod";
                }

                //put edited pattern to the top stack
                array_shift($callable);
                array_unshift($callable, $pathMethod);

                //put edited controller to the bottom stack
                array_pop($callable);
                array_push($callable, "$controller:$methodName");

                call_user_func_array(array(self::$slim, $httpMethod), $callable);
            }
        }
    }
}