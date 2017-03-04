<?php

class classTemplate{

  function show($template,$values=array()){
    try{
      require BASEDIR.'/vendor/Mustache/Autoloader.php';
      Mustache_Autoloader::register();
      $mustache = new Mustache_Engine(array(
        'loader' => new Mustache_Loader_FilesystemLoader((BASEDIR."tpl/views")),
        'partials_loader' => new Mustache_Loader_FilesystemLoader(BASEDIR."tpl/partials"),
      ));
      $tpl = $mustache->loadTemplate($template); // loads __DIR__.'/views/foo.mustache';
      return $tpl->render($values);
    }
    catch(Exception $ex){
      classLogger::log($ex,"asda");
    }
  }

} ?>
