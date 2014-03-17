<?php

Class HomeController extends BaseController
{

    public function welcome()
    {
        $this->data['title'] = 'Welcome to Slim Starter Application';
        App::render('welcome.twig', $this->data);
    }
}