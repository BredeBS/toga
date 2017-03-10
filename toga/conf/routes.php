<?php


$router->get('/',"ModuleHello@home");
$router->get('/index\.(\w+)',"ModuleHello@home");
$router->get('/generator/addAdmin',"ModuleGenerator@addAdmin");
$router->post('/generator/addAdmin',"ModuleGenerator@addAdmin");
 ?>
