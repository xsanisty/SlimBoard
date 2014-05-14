<?php

namespace SlimStarter\Facade;

class DatabaseFacade extends \SlimFacades\Facade{
    protected static function getFacadeAccessor() { return 'db'; }
}