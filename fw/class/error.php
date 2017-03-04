<?php

class classError{

  public static function code404(){
    $template = new classTemplate();
    echo $template->show("fw/404");
  }

} ?>
