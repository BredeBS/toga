<?php


$router->get('/',"ModuleHello@home");
$router->get('/index\.(\w+)',"ModuleHello@home");
 ?>
