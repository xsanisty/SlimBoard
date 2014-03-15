<?php

$config['cookies'] = array(
    'expires'       => '20 minutes',
    'path'          => '/',
    'domain'        => null,
    'secure'        => false,
    'httponly'      => false,
    'name'          => 'slim_session',
    'secret'        => 'CHANGE_ME',
    'cipher'        => MCRYPT_RIJNDAEL_256,
    'cipher_mode'   => MCRYPT_MODE_CBC
);