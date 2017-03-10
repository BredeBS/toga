<?php

class classDB{
  var $debug;
  var $connection;
  var $query;
  var $result;
  var $localeDomain;
  var $utils;
  protected $_Config;

  public function __construct($debug = false) {
    $this->localeDomain = "toga";
    $this->debug        = DBDEBUG;
    $this->utils        = new classUtil();
    if ($debug) {
        $this->debug    = true;
    }
  }

  public function insert($tableName,$fieldsAndValues,$types){
        if(!empty($tableName)){
        $fieldsCount   = count($fieldsAndValues);
        $return = new classReturn();
        if($fieldsCount==count($types)&&$fieldsCount!=0){
          $tempQuery   = "insert into ".$tableName;
          $fieldsSteps = array();
          $paramSteps  = array();
          $step        = 0;

          foreach($fieldsAndValues as $key=>$value){
            $fieldsSteps[]  = $key;
            $querySteps[]   = $key;
            $add = true;
            if($types[$step]=="%d"){
              if(!is_numeric($value)){
                $return->addMessageToCollection(sprintf(dgettext($this->localeDomain,"The field '%s' is not a number"),$key));
                $add = false;
              }
            }
            if($add){
              $processed      = sprintf($types[$step],$value);
              $paramSteps[]   = $processed;
            }
            $step++;
          }
          if(count($fieldsSteps)>0&&count($fieldsSteps)==count($paramSteps)){
            $this->query=$tempQuery." (".join(",",$fieldsSteps).") values ('".join("','",$paramSteps)."')";
            $return = $this->executeQuery();
          }else{
            $return->addMessageToCollection(dgettext($this->localeDomain,"The number of processed fieldsAndValues and types doesn't match."));
          }

        }else{
          $return->addMessageToCollection(dgettext($this->localeDomain,"The number of fieldsAndValues and types doesn't match."));
        }
      }
      else{
        $return->addMessageToCollection(dgettext($this->localeDomain,"You must declare the table."));
      }
      return $return;
      }


  public function update($tableName,$fieldsAndValues,$types,$fieldsAndValuesWhere,$typesWhere,$force=false){
    if(!empty($tableName)){
    $fieldsCount   = count($fieldsAndValues);
    $whereCount    = count($fieldsAndValuesWhere);
    $return = new classReturn();
    if($fieldsCount==count($types)&&$fieldsCount!=0)
    {
      if($whereCount==count($typesWhere))
      {
        $tempQuery   = "update ".$tableName." set ";
        $paramSteps  = array();
        $step        = 0;

        foreach($fieldsAndValues as $key=>$value){
          $add = true;
          if($types[$step]=="%d"){
            if(!is_numeric($value)){
              $return->addMessageToCollection(sprintf(dgettext($this->localeDomain,"The field '%s' is not a number"),$key));
              $add        = false;
            }
          }
          if($add){
            $paramSteps[] = $key."='".sprintf($types[$step],$value)."'";
          }
          $step++;
        }
        $paramStepsWhere   = array();
        $step              = 0;

        foreach($fieldsAndValuesWhere as $key=>$value){
          $add = true;
          if($typesWhere[$step]=="%d"){
            if(!is_numeric($value)){
              $return->addMessageToCollection(sprintf(dgettext($this->localeDomain,"The field '%s' is not a number"),$key));
              $add        = false;
            }
          }
          if($add){
            $paramStepsWhere[] = $key."='".sprintf($typesWhere[$step],$value)."'";
          }
          $step++;
        }
        if(count($fieldsAndValues)>0&&count($fieldsAndValues)==count($paramSteps)){
            if(count($fieldsAndValues)>0&&count($fieldsAndValues)==count($paramSteps)){
              $this->query    =  $tempQuery.join(",",$paramSteps)." where ";
              $where          = join(" and ",$paramStepsWhere);
              $execute        = true;
              if(empty($where)){
                $execute      = $force;
              }
              if($execute){
                $this->query    .= $where;
                $return = $this->executeQuery();
              }else{
                $return->addMessageToCollection(dgettext($this->localeDomain,"The where is empty."));
              }
          }else{
            $return->addMessageToCollection(dgettext($this->localeDomain,"The number of processed fieldsAndValues and types doesn't match."));
          }
        }else{
          $return->addMessageToCollection(dgettext($this->localeDomain,"The number of processed fieldsAndValues and types doesn't match."));
        }
      }else{
        $return->addMessageToCollection(dgettext($this->localeDomain,"The number of fieldsAndValuesWhere and typesWhere doesn't match."));
      }
    }else{
      $return->addMessageToCollection(dgettext($this->localeDomain,"The number of fieldsAndValues and types doesn't match."));
    }
  }
  else{
    $return->addMessageToCollection(dgettext($this->localeDomain,"You must declare the table."));
  }
    return $return;
  }
  function executeOther(){
    $return = $this->connect();
    $data  = mysqli_query($this->connection,$this->query);
    if($data!=false){
      $return->setObject($data);
      p($obj);
    }else{
      $return->addMessageToCollection(sprintf(dgettext($this->localeDomain,"The Query '%s' doesn't look good."),$this->query));
    }
    mysqli_free_result($data);

    return $return;
  }

    function getLastQuery(){
      return $this->query;
    }
      function execute($query){
        $return = new classReturn();
        if(empty($query)){

          $return->addMessageToCollection(dgettext($this->localeDomain,"You must declare a query."));
        }else{
          $this->query = $query;
          $return = $this->executeQuery();
        }
        return $return;
      }
function getOne($tableName,$fieldToSelect,$fieldsAndValuesWhere=array(),$typesWhere=array()){
  $return =  $this->select($tableName,"count(".$fieldToSelect.") as returnValue",$fieldsAndValuesWhere,$typesWhere,0,1);
  if(isset($return->collection)){
    if(is_array($return->collection)){
      $return->collection = $return->collection[0]->returnValue;
    }
  }
  return ($return);
}

  function select($tableName="",$fieldsToSelect="*",$fieldsAndValuesWhere=array(),$typesWhere=array(),$page=0,$pageSize=10){
      if(is_string($fieldsToSelect)){
        $fieldsToSelect = array($fieldsToSelect);
      }
      if(!empty($tableName)){
        $fieldsCount   = count($fieldsToSelect);
        $whereCount    = count($fieldsAndValuesWhere);
        $return = new classReturn();
        if($fieldsCount!=0){
          if($whereCount==count($typesWhere)){
            $paramStepsWhere   = array();
            if(is_array($fieldsAndValuesWhere)){
              $step              = 0;
              foreach($fieldsAndValuesWhere as $key=>$value){
                $add = true;
                if($typesWhere[$step]=="%d"){
                  if(!is_numeric($value)){
                    $return->addMessageToCollection(sprintf(dgettext($this->localeDomain,"The field '%s' is not a number"),$key));
                    $add        = false;
                  }
                }
                if($add){
                  $paramStepsWhere[] = $key."='".sprintf($typesWhere[$step],$value)."'";
                }
                $step++;
              }
            }
            if(count($fieldsAndValuesWhere)==count($paramStepsWhere)){
              $this->query    =  "select ".join(",",$fieldsToSelect)." from ".$tableName;
              $where          = join(" and ",$paramStepsWhere);
              if(!empty($where)){
                $this->query    .= " where ".$where;
              }
              $this->query  .= " limit ".($page*$pageSize).",".$pageSize;
              p($this->query);
              $return = $this->executeQuery();

            }else{
              $return->addMessageToCollection(dgettext($this->localeDomain,"The number of processed fieldsAndValuesWhere and typesWhere doesn't match."));
            }
          }else{
            $return->addMessageToCollection(dgettext($this->localeDomain,"The number of fieldsAndValuesWhere and typesWhere doesn't match."));
          }
        }else{
          $return->addMessageToCollection(dgettext($this->localeDomain,"You must select at least one field from the table ."));
        }
      }else{
        $return->addMessageToCollection(dgettext($this->localeDomain,"You must declare the table."));
      }
      return $return;

  }

  function connect() {
    $return           = new classReturn();
    $return->status   = true;
    $mustGoOn         = true;
    if(empty(DBUSER)){
      $return->addMessageToCollection(dgettext($this->localeDomain,"You must declare your DBUSER"));
      $mustGoOn = false;
    }
    if(empty(DBNAME)){
      $return->addMessageToCollection(dgettext($this->localeDomain,"You must declare your DBNAME"));
      $mustGoOn = false;
    }
    if(empty(DBSERVER)){
      $return->addMessageToCollection(dgettext($this->localeDomain,"You must declare your DBSERVER"));
      $mustGoOn = false;
    }

    if($mustGoOn){
        try {
            $this->connection = @mysqli_connect(DBSERVER,DBUSER,DBPASS);
            if (!$this->connection) {
                $return->addMessageToCollection(dgettext($this->localeDomain,"We can't connect with your DB Server please review your credentials"));
                $mustGoOn = false;
            }
            $dbSelected = mysqli_select_db($this->connection,DBNAME);
            if (!$dbSelected) {
              $return->addMessageToCollection(dgettext($this->localeDomain,"We can't select the DB"));
              $mustGoOn = false;
            }
        } catch (Exception $ex) {
            $return->addMessageToCollection(dgettext($this->localeDomain,"Something happended"));
            $return->addMessageToCollection($ex->getMessage());
            $mustGoOn = false;
            classLogger::log($ex,"Exception in loader");
        }
    }
    return $return;
  }

  function closeConnection() {
      mysqli_close($this->connection);
  }


  private function insertUpdateData($isUpdate = false) {
      $returnData   = new StdClass();
      $return = $this->connect();
      if($return->status){
        mysqli_query($this->connection ,$this->query);
        $insert           = mysqli_insert_id($this->connection );
        $return->status   = true;
        if ($isUpdate){
            $return->object   = $this->mysqli_modified_rows();
        }else{
            $objectInsert             = new StdClass();
            $objectInsert->idInserted = $insert;
            $return->object           = $objectInsert;
        }

        $this->closeConnection();
      }
      return $return;
  }

  function mysqli_modified_rows() {
      $dev = array();
      $info_str = mysqli_info($this->connection );
      $a_rows = mysqli_affected_rows($this->connection );
      $a = preg_split("/ /", $info_str);
      if (count($a) == 9) {
          $dev["paired"] = $a[2];
          $dev["changed"] = $a[5];
          $dev["warnings"] = $a[8];
          return $dev;
      }
      return 0;
  }


  function debug($debug) {
      if (is_int($debug)) {
          if ($debug == 0) {
              $this->debug = false;
          } else
              $this->debug = true;
      }
      else {
          $this->debug = $debug;
      }
  }

  function Connection($debug = false) {
      if (is_int($debug)) {
          if ($debug == 0) {
              $this->debug = false;
          } else
              $this->debug = true;
      }
      else {
          $this->debug = $debug;
      }
  }



  function DB() {
      global $_Conexion;
      return $_Conexion["BaseDeDatos"];
  }
  private function selectData(){
    $return = $this->connect();
    $data  = mysqli_query($this->connection,$this->query);
    if($data!=false){
      while ($responseData = $data->fetch_object()) {
        $return->addItemToCollection($responseData);
      }
      $return->setObject($data);
      p($obj);
    }else{
      $return->addMessageToCollection(sprintf(dgettext($this->localeDomain,"The Query '%s' doesn't look good. (the table exists?)"),$this->query));
    }
    mysqli_free_result($data);

    return $return;
  }
  private function executeQuery() {
    $return = new classReturn();
    if(!empty($this->query)){
      $querySlices = preg_split("/ /", $this->query);
      if(count($querySlices)>0){
        $whatToDo = strtolower($querySlices[0]);
        if ($whatToDo == "select" || $whatToDo == "show" || $whatToDo == "desc") {
          $return = $this->selectData();
        }else if($whatToDo=="insert"){
          $return = $this->insertUpdateData(false);
        }else if ($whatToDo == "update"){
          $return = $this->insertUpdateData(true);
        }else if ($whatToDo == "create" || $whatToDo == "alter"||$whatToDo == "show"||$whatToDo == "desc"){
          $return = $this->executeOther();
        }else{
          if($this->debug){
            $return->addMessageToCollection(sprintf(dgettext($this->localeDomain,"The Query '%s' doesn't match the expected"),$this->query));
          }else{
            $return->addMessageToCollection(dgettext($this->localeDomain,"The Query doesn't match the expected content"));
          }
        }
        if($this->debug){
          $return->addMessageToCollection(sprintf(dgettext($this->localeDomain,"Queries Executed: '%s'"),$this->query));
        }
      }else{
        $return->addMessageToCollection(dgettext($this->localeDomain,"The Query doesn't match the expected format"));
      }
    }else{
        $return->addMessageToCollection(dgettext($this->localeDomain,"The Query is Empty"));
    }
    return $return;
  }

}
?>
