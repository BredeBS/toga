<?php

class classError{

  public static function code404(){
    $template = new classTemplate("toga");
    echo $template->show("404");
  }



} ?>
