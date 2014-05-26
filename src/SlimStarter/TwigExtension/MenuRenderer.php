<?php

namespace SlimStarter\TwigExtension;
use \Slim;

class MenuRenderer extends \Twig_Extension
{
    public function getName()
    {
        return 'menu_renderer';
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('render_menu', array($this, 'renderMenu'))
        );
    }

    public function renderMenu($name, $tag = 'ul', $option)
    {
        $app = Slim::getInstance();

        return $app->menu->render($name, $tag, $option);
    }
}