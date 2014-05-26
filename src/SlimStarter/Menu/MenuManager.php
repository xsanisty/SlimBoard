<?php

namespace SlimStarter\Menu;

use \Illuminate\Support\Collection;
use \Request;

class MenuManager{
    protected $menuCollection;

    public function __construct(){
        $this->menuCollection = new Collection;
    }

    /**
     * Create new menu collection, e.g admin_sidebar, admin_topbar
     * @param  String $name
     */
    public function create($name){
        $this->menuCollection[$name] = new MenuCollection;
        $this->menuCollection[$name]->setName($name);

        return $this->menuCollection[$name];
    }

    public function get($name){
        return $this->menuCollection[$name];
    }

    /**
     * Render specific menu to html
     * @param  string $menu
     * @param  string $tag
     * @return String
     */
    public function render($menu, $tag = 'ul', $options){
        return $this->renderMenu(
            $this->menuCollection[$menu],
            $tag,
            $options,
            $this->menuCollection[$menu]->getActiveMenu(),
            0
        );
    }

    protected function renderMenu(\SlimStarter\Menu\MenuCollection $menu, $tag = 'ul', $options, $active = '', $level=0){
        switch($tag){
            case 'ul':
                $childTag = 'li';
                break;
            case 'div':
                $childTag = 'div';
                break;
            default:
                $childTag = 'div';
                break;
        }

        /** convert array attribute to "attr"="value" "attr2"="value2" format */
        $attribute = '';
        if(isset($options['attributes'])){
            foreach ($options['attributes'] as $key => $value) {
                $attribute.= "$key=\"$value\" ";
            }
        }

        $parentTagFormat  = "<$tag $attribute>%s</$tag>";
        $childTagFormat   = "<$childTag %s >%s <a href=\"%s\" %s >%s %s</a>%s %s</$childTag>";
        $childTag         = '';
        $activeOption     = isset($options['active']) ? $options['active'] : array();


        foreach($menu as $menuItem){

            /** append active class when node is active */
            if($active == $menuItem->getName() || $menuItem->isActive()){
                $class = $menuItem->getAttribute('class');
                $menuItem->setAttribute('class', $class.' active');
            }

            if($active == $menuItem->getName()){
                $class = $menuItem->getAttribute('class');

                $menuItem->setAttribute('class', isset($activeOption['class']) ? $class.' '.$activeOption['class'] : '');
                $menuItem->prependString(isset($activeOption['prepend']) ? $activeOption['prepend'] : '');
                $menuItem->appendString(isset($activeOption['append']) ? $activeOption['append'] : '');
            }

            $childAttribute = $menuItem->getStringAttribute();
            $childIcon      = $menuItem->getIcon() ? '<i class="fa fa-fw fa-'.$menuItem->getIcon().'"></i>' : '';

            if($menuItem->hasChildren()){
                $submenu = $this->renderMenu($menuItem->getChildren(), $tag, array(
                        'attributes'    => $menuItem->getAttribute(),
                        'active'        => $activeOption
                    ), $active, ++$level);
            }else{
                $submenu = '';
            }

            $childTag.= sprintf(
                $childTagFormat,
                $childAttribute,
                $menuItem->getPrependedString(),
                $this->generateUrl($menuItem->getUrl()),
                $menuItem->getLinkStringAttribute(),
                $childIcon,
                $menuItem->getLabel(),
                $menuItem->getAppendedString(),
                $submenu
            );
        }

        return sprintf($parentTagFormat, $childTag);
    }

    /**
     * generate base URL
     */
    protected function generateUrl($urlpath)
    {
        $path       = dirname($_SERVER['SCRIPT_NAME']);
        $path       = trim($path, '/');
        $baseUrl    = Request::getUrl();
        $baseUrl    = trim($baseUrl, '/');
        $baseUrl    = $baseUrl.'/'.$path.( $path ? '/' : '' );

        $urlpath    = trim($urlpath, '/');
        return $baseUrl.$urlpath;
    }
}