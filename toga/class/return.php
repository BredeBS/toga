<?php
class classReturn{

  var $status;
  var $object;
  var $collection;
  var $message;
  private $messageCollection;

  function __construct($status=false,$data="",$message=""){
    $this->messageCollection    = array();
    $this->status               = $status;
    if(!is_string($data)){
      if(is_array($data)){
        $this->collection       = $data;
      }else{
        $this->object           = $data;
      }
    }
    $this->message              = $message;
  }
  function getStatus(){
    return $this->status;
  }
  function getMessageCollection(){
    return $this->messageCollection;
  }
  function getObject(){
    return $this->object;
  }
  function getCollection(){
    return $this->collection;
  }
  function getMessage(){
    return $this->message;
  }
  function setStatus($status){
    $this->status = $status;
  }
  function setObject($object){
    $this->object = $object;
  }
  function setCollection($collection){
    $this->collection = $collection;
  }
  function setMessage($message){
    $this->message = $message;
  }
  function addMessageToCollection($message){
    $this->messageCollection[] = $message;
  }
  function addItemToCollection($item){
    $this->collection[] = $item;
  }


} ?>
