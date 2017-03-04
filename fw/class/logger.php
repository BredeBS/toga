<?php
class classLogger{

  static function log($parameters,$extraMessage=""){
    $message        = "";
    $message        .= print_r($_SERVER,true);
    $message        .= print_r(func_get_args(),true);
    classLogger::saveLog($message);
  }
  static function saveLog($message){
    $date       = new DateTime();
    $newMessage = "\n == TOGAFW LOG == \n";
    $newMessage .= "\n Date: ".$date->format("Y/m/d H:i:s")." \n";
    $newMessage .= $message;
    $newMessage .= "\n == !TOGAFW LOG == \n";
    $base       = BASEDIR."logs/";
    $file       = $base.$date->format("Y/m/d/His").rand(45,99999).".log";
    $fileYear   = $base.$date->format("Y");
    $fileMonth  = $base.$date->format("Y/m");
    $fileDay    = $base.$date->format("Y/m/d");
    classUtil::createDir($fileYear);
    classUtil::createDir($fileMonth);
    classUtil::createDir($fileDay);
    file_put_contents($file,$newMessage);
  }

} ?>
