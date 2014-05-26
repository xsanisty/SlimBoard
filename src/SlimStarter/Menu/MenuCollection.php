<?php

namespace SlimStarter\Menu;

use \Illuminate\Support\Collection;

class MenuCollection extends Collection{
    protected $active;
    protected $name;

    public function getName(){
        return $this->name;
    }

    public function setName($name){
        $this->name = $name;
    }

    public function setActiveMenu($menu){
        //echo '<br><br><br>Setting '.$menu.' as active menu<br><br><br>';
        $this->active   = $menu;

        foreach($this->items as $item){
            $this->seekAndActivate($item, $menu);
        }
    }

    protected function seekAndActivate(\SlimStarter\Menu\MenuItem $item, $menu){
        if($item->getName() == $menu){
            $item->setActive(true);
        }else if($item->hasChildren()){
            foreach($item->getChildren() as $child){
                $this->seekAndActivate($child, $menu);
            }
        }else{
            $item->setActive(false);
        }
    }

    public function getActiveMenu(){
        return $this->active;
    }

    /**
     * Add new item to menuCollection
     * @param SlimStarter\Menu\MenuItem $item
     * @param String $menu
     */
    public function addItem($name, \SlimStarter\Menu\MenuItem $item){
        $this->items[$name] = $item;
    }

    public function getItem($name){
        return isset($this->items[$name]) ? $this->items[$name] : null;
    }

    /**
     * MenuItem factory
     * @param  String $label
     * @param  String $url
     * @return MenuItem
     */
    public function createItem($name, $option){
        return new MenuItem($name, $option);
    }
}
