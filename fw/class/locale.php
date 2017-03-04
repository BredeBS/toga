<?php



function _e($message){
  echo __($message);
}
function __($message){
  if(isset($text[$message])){
    return $text[$message];
  }else{
    return $message;
  }
}
