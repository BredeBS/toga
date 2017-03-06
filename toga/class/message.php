<?php
class message{
  var $code;
  var $text;
  var $return;
  var $data;
  function __construct($code,$return,$text="",$data=""){
    $this->code   = $code;
    $this->return = $return;
    $this->text   = $text;
    $this->data   = $data;
  }
} ?>
