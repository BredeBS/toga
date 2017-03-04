<?php

class classUtil{
  public static function p($data,$json=false){
    if($json)
      echo "\n";
    else
      echo '<pre>';
    print_r($data);
    if($json)
      echo "\n";
    else
      echo '</pre>';
  }
  public static function _remove_empty_internal($value) {
    return !empty($value) || $value === 0;
  }
  public static function createDir($dir){
    if(!file_exists($dir)){
      mkdir($dir);
    }
  }
}
if(!function_exists("p")){
  function p($a){
    classUtil::p($a);
  }
}


 ?>
