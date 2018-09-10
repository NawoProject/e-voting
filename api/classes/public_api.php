<?php 
//Vote class
//0. Register new vote with public key and parameters. 
//1. Get user private key with correct secret and code
//2. cast vote with private key
//3. Decrypt votes
//4. Tabulate votes
//5. Show vote results

class na_public_api{

    //attributes
  private $db_server;
  private $db_name;
  private $db_username;
  private $db_pass;
  private $comp_path;
  private $serverName = "Local testing server";


  //include database functions in connects
   function __construct(){
     require('../config/na_config.php'); //require configuration file
     require($comp_path); //require composer mongodb lib
     $this->db_name = $db_name;
     $this->db_username = $db_username;
     $this->db_pass = $db_pass;
     $this->db_server = $db_server;
   } //end construct

   function load_votes($election_id){
    $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $collection = $client->$x->{$election_id."_vroll"}; //Select collection
    
    $query = $collection->find(
        [
            'block_hash' => ['$ne' => NULL],
            'vote' => ['$ne' => NULL],
            'signature' => ['$ne' => NULL]
        ],
        [
            'projection' => 
            [
                'user_hash' => 1,
                'ward' => 1,
                'lga' => 1,
                'state' => 1,
                'pb_key' => 1,
                'vote' => 1,
                'signature' => 1,
                'timestamp' => 1
            ],
            [
                'sort' => ['timestamp' => 1]
            ]
        ]
    );
    $res = array();
    foreach($query as $r){
        $res[] = $r;
    }
    return $res;
   } //end function

   function validate_hash($election_id, $user_hash, $block_hash){
    $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $collection = $client->$x->{$election_id."_vroll"}; //Select collection
    
    $query = $collection->findOne(
        [
            'user_hash' => $user_hash,
            'block_hash' => $block_hash,
        ],
        [
            'projection' => 
            [
                'user_hash' => 1,
                'block_hash' => 1
            ]
        ]
    );
    if(isset($query->block_hash)){
        $res = $query->block_hash;
    } else {
        $res = FALSE;
    }
    return $res;
   }


}//end class 