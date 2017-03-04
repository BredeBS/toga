<?php


$router->get('/',"ModuleSaludo@home");
$router->get('/index\.(\w+)',"ModuleSaludo@home");
$router->get('/hola/(\w+)',"ModuleSaludo@hola");
$router->get('/chao/([A-Za-z]+)',"ModuleSaludo@chao");
 ?>
