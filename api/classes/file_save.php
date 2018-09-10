<?php 
//Class to save files


class na_save{

    private $comp_path;
    private $aws_keys;


  //include database functions in connects
   function __construct(){
     require('../config/na_config.php'); //require configuration file
     require($comp_path); //require composer mongodb lib
     $this->aws_keys = $aws_keys;
   } //end construct


    private function save_local($name, $data){
        $fp = fopen("../files/$name.txt", 'w');
        fwrite($fp, $data);
        fclose($fp);
        return "$name.txt";
    }

    function save_key($location, $name, $key){
        if ($location == "local"){
            $saved = $this->save_local($name, $key); 
        } elseif ($location == 'aws') {
            $saved = $this->save_aws($name, $key);
        }

        return $saved;
    } //end function


    function save_aws($name, $data){
        $aws = new Aws\S3\S3Client();

        $client = $aws->factory(array(
            'profile' => '<profile in your aws credentials file>'
        ));

        $bucket = "KeyStore";
        $result = $client->putObject(array(
            'Bucket' => $bucket,
            'Key'    => "$name.txt",
            'Body'   => "$data"
        ));
    }// end function
} //end class


?>