<?php

Class HomeController{

    public function welcome(){
        View::display('welcome.twig', array(
            'title' => 'Hello Slim!'
        ));
    }
}