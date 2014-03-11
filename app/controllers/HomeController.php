<?php

Class HomeController extends BaseController{

    public function welcome(){
        $this->data['title'] = 'Some title';
        App::render('welcome.twig', $this->data);
    }
}