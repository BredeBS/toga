<?php

class classTemplate{

    var $styles;
    var $scripts;
    var $mustache;
    var $isAdmin;
    var $dataHeader;
    function __construct(){
      $this->styles   = array();
      $this->scripts  = array();
      $this->dataHeader  = array();
      require BASEDIR.'/vendor/Mustache/Autoloader.php';
      Mustache_Autoloader::register();
      $this->mustache = new Mustache_Engine(array(
        'loader' => new Mustache_Loader_FilesystemLoader((BASEDIR."tpl/views")),
        'partials_loader' => new Mustache_Loader_FilesystemLoader(BASEDIR."tpl/partials"),
      ));
    }

    function show($template,$templateHeader="toga/header",$templateFooter="toga/footer",$values=array()){
      try{
        $tpl       = $this->mustache->loadTemplate($template); // loads __DIR__.'/views/foo.mustache';
        if($this->isAdmin){
          $this->registerScript("assets/js/jquery-3.1.1.min.js");
          $this->registerStyle("assets/css/bootstrap.min.css");
          $this->registerScript("assets/js/bootstrap.min.js");
        }
        $return    = $this->header($templateHeader);
        $return   .= $tpl->render($values);
        return $return;
      }
      catch(Exception $ex){
        classLogger::log($ex,"asda");
      }
    }
    function setAdmin($admin=false){
      $this->isAdmin = $admin;
    }
    function setHeaderData($key,$values){
      if($key!="styles"&&$key!="scripts")
        $this->dataHeader[$key]=$values;
    }
    function setPageTitle($title){
        $this->setHeaderData("title",$title);
    }

    function registerScript($path){
      $finalPath = BASEDIR.$path;
      if(file_exists($finalPath)){
        $item             = array("path"=>URIFW."/".$path);
        $this->scripts[]  = $item;
      }else{
        classLogger::log("not found script (".$path.")");
      }
    }

    public function registerStyle($path){
      $finalPath = BASEDIR.$path;
      if(file_exists($finalPath)){
        $item           = array("path"=>URIFW."/".$path);
        $this->styles[] = $item;
      }else{
        classLogger::log("not found style (".$path.")");
      }
    }

    public function header($templateHeader="toga/header"){
      $this->dataHeader["scripts"]  = $this->scripts;
      $this->dataHeader["styles"]   = $this->styles;
      $tpl                          = $this->mustache->loadTemplate($templateHeader);
      return $tpl->render($this->dataHeader);
    }

}

?>
