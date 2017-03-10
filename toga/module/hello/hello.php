<?php
class moduleHello{
  var $localeDomain;
  var $moduleName ;
  function __construct(){
    $this->localeDomain = "hello";
    $this->moduleName = "hello";
  }
  function home($ext=""){
    if(!empty($ext)){
        echo  json_encode(array("status"=>"json"));
    }else{
      $template = new classTemplate($this->moduleName);
      $template->setPageTitle(dgettext($this->localeDomain,"Home - Toga Framework"));
      $template->registerScript("assets/js/jquery-3.1.1.min.js");
      $template->registerStyle("assets/css/bootstrap.min.css");
      $template->registerScript("assets/js/bootstrap.min.js");

      $template->setHeader("header");
      $template->setFooter("footer");
      echo $template->show("home");
    }
  }


} ?>
