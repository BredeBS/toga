<?php

class classTemplate{

    var $styles;
    var $scripts;
    var $mustache;
    var $dataScriptsAndStyles;
    var $showHeader;
    var $nameHeader;
    var $showFooter;
    var $nameFooter;
    function __construct($module){
      $this->styles                 = array();
      $this->scripts                = array();
      $this->dataScriptsAndStyles   = array();
      $this->module                 = $module;
      $this->showFooter             = false;
      $this->showHeader             = false;
      require BASEDIR.'/vendor/Mustache/Autoloader.php';
      Mustache_Autoloader::register();
      $this->mustache = new Mustache_Engine(array(
        'loader' => new Mustache_Loader_FilesystemLoader((MODULEDIR.$this->module ."/views"))
      ));
    }
    function setHeader($name){
      $this->showHeader = true;
      $this->nameHeader = $name;
    }
    function setFooter($name){
      $this->showFooter = true;
      $this->nameFooter = $name;
    }

    function show($template,$params=array()){
      try{
        $return       = "";
          if($this->showHeader){
          $return     = $this->header($this->nameHeader);
        }
        $tpl          = $this->mustache->loadTemplate($template); // loads __DIR__.'/views/foo.mustache';
        $return      .= $tpl->render($params);
        if($this->showFooter){
          $return    .= $this->footer($this->nameFooter);
        }
        return $return;
      }
      catch(Exception $ex){
        p($ex);
        classLogger::log($ex,"showModule");
      }
    }
    function setHeaderData($key,$values){
      if($key!="styles"&&$key!="scripts")
        $this->dataScriptsAndStyles[$key]=$values;
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

        public function header($templateHeader="header"){
          $this->dataScriptsAndStyles["styles"]   = $this->styles;
          $tpl                          = $this->mustache->loadTemplate($templateHeader);
          return $tpl->render($this->dataScriptsAndStyles);
        }
        public function footer($templateFooter="footer"){
          $this->dataFooter["scripts"]  = $this->scripts;
          $tpl                          = $this->mustache->loadTemplate($templateFooter);
          return $tpl->render($this->dataFooter);
        }

}

?>
