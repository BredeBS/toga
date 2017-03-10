<?php
class moduleGenerator{

  var $moduleName ;
  var $localeDomain ;
  var $template;
  function __construct(){
    $this->moduleName = "generator";
    $this->localeDomain = "generator";
  }
  function addBasicLibraries(){
    $this->template->registerScript("assets/js/jquery-3.1.1.min.js");
    $this->template->registerStyle("assets/css/bootstrap.min.css");
    $this->template->registerScript("assets/js/bootstrap.min.js");
  }
  function addAdmin(){
    $templateVars = new StdClass();
    $errors = array();
    if(!defined("DBUSER")||"" ===constant("DBUSER")){
      $errors[] = array("message"=>dgettext($this->localeDomain,"You must declare your DBUSER"));
    }
    if(!defined("DBNAME")||"" ===constant("DBNAME")){
      $errors[] = array("message"=>dgettext($this->localeDomain,"You must declare your DBNAME"));
    }
    if(!defined("DBSERVER")||"" ===constant("DBSERVER")){
      $errors[] = array("message"=>dgettext($this->localeDomain,"You must declare your DBSERVER"));
    }
    if(count($errors)>0){
      $templateVars->errors      = $errors;
    }else{
      $templateVars->messages             = new StdClass();
      $templateVars->statusPost           = false;
      $fieldsForm   = array();

      if(isset($_POST["submit"])){

        $togadb = new classDB();
        $expectedFields = array();
        $itemsValidation["yourName"]        = array("filter"=>FILTER_VALIDATE_STRING);
        $itemsValidation["yourEmail"]       = array("filter"=>FILTER_VALIDATE_STRING);
        $itemsValidation["yourPassword"]    = array("filter"=>FILTER_VALIDATE_STRING);
        $validation = filter_input_array(INPUT_POST, $itemsValidation);
        p($validation);

        if(!empty($validation["yourName"])&&!empty($validation["yourEmail"])&&!empty($validation["yourPassword"])){
          p("all ok");
          $TogaDB = new classDB();
          $tablesAlreadyExists = $TogaDB->execute("SHOW TABLES");
          $tableExists =false;
          if(!empty($tablesAlreadyExists->collection)){
            foreach($tablesAlreadyExists->collection as $key=>$value){

              if($value->{"Tables_in_".DBNAME}==DBPREFIX."user"){
                $tableExists = true;
              }
            }
          }
          p($responseQuery);
          if(!$tableExists){
            $queryCreate   = "CREATE TABLE ".DBPREFIX."user (iduser int(11) NOT NULL,  username varchar(255) NOT NULL,  email varchar(255) NOT NULL,  password varchar(255) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
            $responseQuery = $TogaDB->execute($queryCreate);
            if($responseQuery->status){
              p($responseQuery);
              $queryCreate ="ALTER TABLE ".DBPREFIX."user ADD UNIQUE KEY iduser (iduser);";
              $responseQuery = $TogaDB->execute($queryCreate);
              if($responseQuery->status){
                p($responseQuery);
                $queryCreate ="ALTER TABLE ".DBPREFIX."user MODIFY iduser int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1";
                $responseQuery = $TogaDB->execute($queryCreate);
                if($responseQuery->status){
                  $magic =  uniqid('togafw_');
                  p($magic);
                  $fileConfig = BASEDIR."conf/config.inc.php";
                  $current = file_get_contents($fileConfig);
                  $current .= "\ndefine(\"MAGIC\",\"".$magic."\")\n";
                  file_put_contents($fileConfig, $current);
                  //togafw_58c228064ee7c
                  p($responseQuery);
                  $resultInsert = $togadb->insert(DBPREFIX."user",array("username"=>$validation["yourName"],"email"=>$validation["yourEmail"],"password"=>md5($magic.$validation["yyourPasswordourName"])),array("%s","%s","%s"));
                  if($responseQuery->status){
                    $templateVars->statusPost         = true;
                  }else{
                    $templateVars->messages->errorMessage               = dgettext($this->localeDomain,"Something happened when we try to insert the user");
                  }
                }
              }
            }
          }else{
            $templateVars->messages->errorMessage               = sprintf(dgettext($this->localeDomain,"The Table '%s' already exists"),DBPREFIX."user");
          }
        }else{
          $emptyFieldsArray=array();
          foreach($validation as $key=>$value){
            if(empty($value)){
              $emptyFieldsArray[] = array("field"=>$key);
            }
          }

          $templateVars->messages->errorMessage               = dgettext($this->localeDomain,"Please fill all the fields");
          $templateVars->messages->emptyFields                = $emptyFieldsArray;
        }
        //$resultInsert = $togadb->insert("test",array("name"=>"brede","test"=>"prueba","number"=>"1"),array("%s","%s","%d"));
        //p($resultInsert);
        //$resultUpdate = $togadb->update("test",array("name"=>"brede","test"=>"prueba"),array("%s","%s"),array("number"=>"1"),array("%d")); //here
        //p($resultUpdate);
        //$results = $togadb->select("test",array("*"));
        //$results = $togadb->getOne("test","*",array("number"=>"1"),array("%d"));
        //p($results);

      }
    }
    if(!$templateVars->statusPost){

        $templateVars->messages->yourNameLabel                  = dgettext($this->localeDomain,"Your Name");
        $templateVars->messages->yourNamePlaceholder            = dgettext($this->localeDomain,"Please enter your name");
        $templateVars->messages->yourEmailAddressLabel          = dgettext($this->localeDomain,"Your E-mail");
        $templateVars->messages->yourEmailAddressPlaceholder    = dgettext($this->localeDomain,"Please enter your email");
        $templateVars->messages->yourPasswordLabel              = dgettext($this->localeDomain,"Your Password");
        $templateVars->messages->yourPasswordPlaceholder        = dgettext($this->localeDomain,"Please enter your password");
        $templateVars->messages->createLabel                    = dgettext($this->localeDomain,"Create Admin Area");
    }

    $this->template = new classTemplate($this->moduleName);
    $this->addBasicLibraries();
    $this->template->setPageTitle(dgettext($this->localeDomain,"Code generator - Add Admin Area"));
    $this->template->setHeader("header");
    $this->template->setFooter("footer");
    if(!$templateVars->statusPost){
      echo $this->template->show("addadmin",$templateVars);
    }else{
      echo $this->template->show("addadminSuccess",$templateVars);
    }
  }

} ?>
