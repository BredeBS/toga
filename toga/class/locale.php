<?php
/*
function _e($message){
  echo __($message);
}
function __($message,$textDomain=""){
  global $translations;
  if(!empty($textDomain)){
    $fileLocale = BASEDIR."locale/".LOCALE."/".$textDomain.".php";
    if(file_exists($fileLocale)){
      include_once($fileLocale);
      foreach($$textDomain as $name => $locale){
        if(!is_array($translations[$$textDomain]))
          $translations[$textDomain]=array();
        $translations[$textDomain][$name] = $locale;
      }
    }
  }
  if(isset($translations[$textDomain][$message])){
    return $translations[$textDomain][$message];
  }else{
    return $message;
  }
}
*/

putenv("LANG=".LOCALE);
setlocale(LC_ALL, LOCALE);
$dirLocaleModules = BASEDIR."module/";

$localesInDir = scandir($dirLocaleModules);

foreach($localesInDir as $n=>$path){
  if($path!="."&&$path!=".."){
    if(is_dir($dirLocaleModules.$path)){
    bindtextdomain($path, $dirLocaleModules.$path."/locale/");
    }
  }
}
bindtextdomain("toga", BASEDIR."locale");
textdomain("toga");
function __($message,$textDomain="toga"){

  return dgettext($textDomain,$message);
}
