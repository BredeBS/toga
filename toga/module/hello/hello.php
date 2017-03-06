<?php
class moduleHello{
  var $domain;
  function __construct(){
    $this->domain = "hello";
  }
  function home($ext=""){
    if(!empty($ext)){
        echo  json_encode(array("status"=>"json"));
    }else{
      $template = new classTemplate();
      $template->setPageTitle(dgettext($this->domain,"Home - Toga Framework"));

      $template->setAdmin(true);
      echo $template->show("toga/home");
    }
  }


} ?>
