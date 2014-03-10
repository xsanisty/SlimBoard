<?php

Class HomeController extends BaseController{

    public function welcome(){
        $this->data['title'] = 'Some title';
        $this->app->render('welcome.twig', $this->data);
    }
}