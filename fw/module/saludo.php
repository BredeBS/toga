<?php
class moduleSaludo{
  function home($ext=""){
    if(!empty($ext)){
        echo  json_encode(array("status"=>"json"));
    }else{
      $template = new classTemplate();
      echo $template->show("fw/home");
    }
  }
  function hola($a){
    p("hola running");
    p($a);
  }
  function chao($a){
    p("chao running");
    p($a);
  }

} ?>
