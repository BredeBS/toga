<?php
class classLogger{

  static function log($extraMessage=""){
    $message         = $extraMessage."\n";
    $message        .= print_r($_SERVER,true);
    $e = new Exception();
    $e->getTraceAsString();
    $message        .= classException::getExceptionTraceAsString($e);
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
