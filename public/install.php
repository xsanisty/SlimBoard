<?php
define ('INSTALL', 1);

require '../app/bootstrap/start.php';

Route::get('/', 'InstallController:index');
Route::post('/db/check', 'InstallController:checkConnection');
Route::post('/db/configure', 'InstallController:writeConfiguration');

Route::get('/finish', 'InstallController:finish');

App::run();