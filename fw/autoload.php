<?php
define("BASEDIR",dirname(__FILE__,1)."/");
spl_autoload_register(function ($className) {
    $base = "";
    preg_match("/class|module/i",$className,$temp);
    if(count($temp)>0){
        $base = strtolower($temp[0]);
        $className = strtolower(preg_replace("/".$temp[0]."/i","",$className));
        $file = BASEDIR.$base."/".$className . '.php';
        if(file_exists($file))
          include $file;
      }
});
try{
  $util = new classUtil();
  $router = new classRouter();
  $router->before('GET', '/.*', function () {
          header('X-Powered-By: bredebs/togafw');
  });
  include_once(BASEDIR."conf/routes.php");
  $router->set404(function() {
      header('HTTP/1.1 404 Not Found');
      $error = new ClassError();
      $error->code404();
  });
  $router->run();
}
catch(Exception $ex){
  classLogger::log($ex,"asda");
}
?>
