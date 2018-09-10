<?php

/*
Class to Initialize elections
Functions
1. Generate election parameters (Administrator details, Party, Nomination End Date, Election Start date/time, Election End date/time, Election private and public keys, wards, lgas, states, all, Initial candidates, Criteria)
2. Generate voters registers
3. Generate private and public keys for voters
4. Gen key pair code database
5. 
*/


class na_init{

        //attributes
  private $db_server;
  private $db_name;
  private $db_username;
  private $db_pass;
  private $comp_path;
  private $stor_path;
  private $serverName = "Local testing server";


  //include database functions in connects
   function __construct(){
     require('../config/na_config.php'); //require configuration file
     require($comp_path); //require composer mongodb lib
     $this->db_name = $db_name;
     $this->db_username = $db_username;
     $this->db_pass = $db_pass;
     $this->db_server = $db_server;
     $this->stor_path = $stor_path;
   } //end construct


   private function get_ward_filter($ward_list){
       if($ward_list[0]=="ALL"){
           $ret = "";
       } else {
           $ret = '"
           ';
           foreach($ward_list as $x){
               $ret = $ret." 
               \"ward\" => \"$x\", ";
           } // end foreach

           $ret = $ret."\"";
       }
       return $ret;
   } 

    //Generate new public private key pair.   
   private function gen_voter_keys(){
    //gen voter public and private keys and base64 encode
    $key = new key_mgt;
    $getkeys = $key->get_new_keys();
    $pbkey = $getkeys[1];
    $pvkey = $getkeys[0];
    $ret = array($pvkey, $pbkey);
    return $ret;
   } 

   //Prepare database. Delete if exists. Remove old codes. Create index.
   private function prep_db($election){
    $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $collection = $client->$x->{$election."_vroll"}; //Select collection

    //drop existing in db
    $deleteResult = $collection->drop();

    //create unique indexes
    $result = $collection->createIndex(['user_hash' => 1],['unique' => 'true' ]);

    //remove old codes from code_db
    $coll2 = $client->$x->code_db;
    $remove = $coll2->deleteMany(
        ["election_id" => "$election"]
    );
    return $result;
   }

   //Add unique batch to voter roll
   private function add_to_voter_roll($election, $data){
        //select valid voters from db filtered by $filter
    $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $collection = $client->$x->{$election."_vroll"}; //Select collection

    $res = $collection->insertMany($data);
    $c = $res->getInsertedCount();
    return $c;
   }

   //add private key to code db.
   private function add_to_code_db($data){
    //select valid voters from db filtered by $filter
    $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $collection = $client->$x->code_db; //Select collection

    $res = $collection->insertMany($data);
    $c = $res->getInsertedCount();
    return $c;
} // end function


private function get_all_valid_voters(){
    //select valid voters from db filtered by $filter
    $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $collection = $client->$x->na_publicdb; //Select collection

    //get initialization date
    $cls = new na_clean;
    $today = $cls->new_time();

    $query = $collection->find(
        [
           'expiry' => ['$gte'=>$today], 
        ],
        [
          'projection' => [
              'user_hash' => 1,
              'ward' => 1,
              'lga' => 1,
              'state' => 1
          ]
        ]
        );
        $res = array();
    foreach($query as $q){
        $res[] = $q;
    }
    return $res;
}

 function get_all_state_voters($state){
    //select valid voters from db filtered by $filter
    $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $collection = $client->$x->na_publicdb; //Select collection

    //get initialization date
    $cls = new na_clean;
    $today = $cls->new_time();

    $query = $collection->find(
        [
           //'expiry' => ['$gte'=>$today],
           'state' => ['$in' => $state]
        ],
        [
          'projection' => [
              'user_hash' => 1,
              'ward' => 1,
              'lga' => 1,
              'state' => 1
          ]
        ]
        );
        $res = array();
        foreach($query as $q){
            $res[] = $q;
        }
        return $res;
    } //end function

private function get_all_lga_voters($lga){
    //select valid voters from db filtered by $filter
    $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $collection = $client->$x->na_publicdb; //Select collection

    //get initialization date
    $cls = new na_clean;
    $today = $cls->new_time();

    $query = $collection->find(
        [
           'expiry' => ['$gte'=>$today],
           'lga' => ['$in' => $lga] 
        ],
        [
          'projection' => [
              'user_hash' => 1,
              'ward' => 1,
              'lga' => 1,
              'state' => 1
          ]
        ]
        );
        $res = array();
    foreach($query as $q){
        $res[] = $q;
    }
    return $res;
}

private function get_all_ward_voters($ward){
    //select valid voters from db filtered by $filter
    $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $collection = $client->$x->na_publicdb; //Select collection

    //get initialization date
    $cls = new na_clean;
    $today = $cls->new_time();

    $query = $collection->find(
        [
           'expiry' => ['$gte'=>$today],
           'ward' => ['$in' => $ward]
        ],
        [
          'projection' => [
              'user_hash' => 1,
              'ward' => 1,
              'lga' => 1,
              'state' => 1
          ]
        ]
        );
    $res = array();
    foreach($query as $q){
        $res[] = $q;
    }
    return $res;
}

private function get_election_details($election_id){
    $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $collection = $client->$x->na_elections; //Select collection

    $query = $collection->findOne(
        [
           'name' => "$election_id"
        ],
        [
          'projection' => [
              'unit' => 1,
              'unit_names' => 1
          ]
        ]
        );
    return $query;
}

   
   function gen_voter_roll($election_id){

    //check valid gen time
    $cl = new na_clean;
    $curr = $cl->new_time();
    $dead = $this->get_election_deadline($election_id);

    if($curr<$dead){
        $message = "Not yet time for initialization. Deadline to update voter status must pass";
        return $message;
        exit;
    }


    //get election type and details
    $e_details = $this->get_election_details($election_id);
    $type = $e_details->unit;
    $name = $e_details->unit_names;
    
    //convert to indexed array;
    $name2 = array();
    foreach($name as $n){
        $name2[] = strtolower($n);
    }
    $name = $name2;
    
    //prep database
    $prep = $this->prep_db($election_id);

    //get voter list 
    $wlist = new na_wards;
    if ($type == "national"){
        $voters = $this->get_all_valid_voters();

    } elseif($type == "state") {
        $voters = $this->get_all_state_voters($name);

   } elseif($type == "lga"){
    $voters = $this->get_all_lga_voters($name);
        
    } elseif ($type == "ward"){
        $voters = $this->get_all_ward_voters($name);
        
    } else {
        $voters = array();
    }

    if(count($voters)==0){
        return "No valid voters $type";
        exit;
    }

    
    //Generate keys for each voter
    $ins1 = 0;
    $ins2 = 0;

    $voter_roll = array();
    $pv_key_db = array();
    $key = new key_mgt;

   foreach($voters as $res){
       $user_hash = $res->user_hash;
       $ward = $res->ward;
       $lga = $res->lga;
       $state = $res->state;
       $keys = $this->gen_voter_keys();
       $pvkey = $keys[0];
       $pbkey = $keys[1];

      $random_bytes = $key->gr_bytes();
      
       $pub = array(
           "user_hash" => $user_hash,
           "ward" => $ward,
           "lga" => $lga,
           "state" => $state,
           "pb_key" => $pbkey
       );

       $pri = array(
          "user_hash" => $user_hash,
          "pv_key" => $pvkey,
          "code" => $random_bytes,
          "election_id" => $election_id
       );


       $voter_roll[] = $pub;
       $pv_key_db[] = $pri;
  }  //end for each query

  $ins1 = $ins1 + $this->add_to_voter_roll($election_id, $voter_roll);
  $ins2 = $ins2 + $this->add_to_code_db($pv_key_db);
//end

    //Check if election keys already exist.

    $check = $this->check_election_key_exists($election_id);
    if ($check[0] === TRUE){
        $elec_pbkey = $check[1];
    } else {

    //Get election key pair. Save private key to location. Return public key
    $vkeys = $this->gen_voter_keys();

    //save private key to location
    $fsave = new na_save;
    $location = $this->stor_path;
    $save_key = $fsave->save_key($location, $election_id, $vkeys[0]);

    $elec_pbkey = $vkeys[1];
    //save election public key to elections database
        $skey = $this->update_pubkey($election_id, $elec_pbkey);
        } //end else
       
    $ret = array($elec_pbkey, $ins1, $ins2);
    return $ret;
   } //end function

    private function update_pubkey($election_id, $pbkey){
        $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $collection = $client->$x->na_elections; //Select collection
    $query = $collection->updateOne(
        [
           'name' => $election_id
        ],
        [
          '$set' => [
              'pbkey' => "$pbkey",
              ]
        ]
        );
    return $pbkey;
   } //end function

   function check_election_key_exists($election_id){
       //select valid voters from db filtered by $filter
    $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $collection = $client->$x->na_elections; //Select collection

    $query = $collection->findOne(
        [
           'name' => $election_id
        ],
        [
          'projection' => [
              'pbkey' => 1,
              ]
        ]
        );
    if(isset($query->pbkey)){
        $ret = array("TRUE", $query->pbkey);

    } else {
        $ret = array("FALSE");
    }
    return $ret;
   }

   private function get_election_deadline($election_id){
    $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $collection = $client->$x->na_elections; //Select collection

    $election_id = strtolower($election_id);
    $res = $collection->findOne(
        [
            "name" => $election_id,
        ],
        [
            'projection' => [
                'deadline' => 1
            ]
        ]
    );
    if(isset($res->deadline)){
        $dl = $res->deadline;
    } else {
        $dl = "";
    }
    return $dl;
   }//end function


} //end class