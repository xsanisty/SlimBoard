<?php

namespace SlimStarter\Facade;

class SentryFacade extends \SlimFacades\Facade{
    protected static function getFacadeAccessor() { return 'sentry'; }
}