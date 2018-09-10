<?php
/*
* Class for authenticating client access to API
* Includes functions for:
* - Generating new API keys - done
* - validating API keys -  done
* - revoking API keys - done
* - Api access stats - pending
* - API keys and key stats stored in Databse
*/

// API auth class

class api_auth {

  //attributes
  private $slength = 25;
  private $db_server;
  private $db_name;
  private $db_username;
  private $db_pass;
  private $comp_path;

 //include database functions in connects
  function __construct(){
    require_once('../config/na_config.php'); //require configuration file
    require_once($comp_path); //require composer mongodb lib
    $this->db_name = $db_name;
    $this->db_username = $db_username;
    $this->db_pass = $db_pass;
    $this->db_server = $db_server;
  }


  // genrate random api key
  private function get_keys() {
    //gen private key
    $randb = openssl_random_pseudo_bytes($this->slength); //declare user
    $randb = bin2hex($randb);
      return $randb;
    } //end function

    //Function to get new api
    public function get_new_api($email){
      //encrypt email
      $enc = new na_security;
      $email = $enc->enc_value($email);

      
      $valid = 0;
      while ($valid != 1) { //Generate apikey and check if it is in db. If it is generate new key

        $apikey = $this->get_keys(); //Generate random string

        $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
        $x = $this->db_name;
        $collection = $client->$x->apikeys; //Select collection
        $apikeyhash = hash('sha256', $apikey);
        $document = array(
          "apikey" => "$apikeyhash"
        );
        $result = $collection->findOne($document);

        if($result==""){
          $document2 = array(
            "apikey" => "$apikeyhash",
            "api_user" => "$email"
          );
          $collection->insertOne($document);
          $valid = 1;
        }


      }
        return TRUE;
    } //end function

    //Validate API key
    public function val_api($apikey){

          $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
          $x = $this->db_name;
          $collection = $client->$x->apikeys;
          $apikeyhash = hash('sha256', $apikey);
          $document = array(
            "apikey" => "$apikeyhash"
            );
          $result = $collection->findOne($document);

        if($result != ""){
          $valid = TRUE;
        } else {
          $valid = FALSE;
        }
        return $valid;
        } // end function


        //Revoke Api key
    public function revoke_api($apikey){
      $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
      $x = $this->db_name;
      $collection = $client->$x->apikeys;
      $apikeyhash = hash('sha256', $apikey);
      $document = array(
        "apikey" => "$apikeyhash"
        );
      $deleteResult = $collection->deleteOne($document);
      $result = $deleteResult->getDeletedCount();
      return $result;
    } //end function


    function create_index(){
      $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
      $x = $this->db_name;
      $collection = $client->$x->na_elections;
      $result = $collection->createIndex(['name' => 1],['unique' => true]);

      return $result;
    } //end function

} // end class

 ?>
