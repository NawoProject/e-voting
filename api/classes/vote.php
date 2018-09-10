<?php 
//Vote class
//0. Register new vote with public key and parameters. 
//1. Get user private key with correct secret and code
//2. cast vote with private key
//3. Decrypt votes
//4. Tabulate votes
//5. Show vote results

class na_vote{

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


   function create_election($name, $position, $reg_deadline, $start, $end, $type, $unit, $unit_names){
    //name - name of elections
    //position being contested or referendum question for referendum
    //reg_deadline - deadline for voter registration. used to intialize elections.
    // start - election start date
    // end - election end date
    //type - election or referendum
    //unit - national, state, lga, or ward
    //unit_names array of names of states or lgas or wards

    $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $collection = $client->$x->{"na_elections"}; //Select collection

    $crs = new na_clean;
    $tnow = $crs->new_time();

    //check if referendum

    if($type == "referendum"){
        $options = array("A" => "Yes", "B" => "No");
    } else {
        $options = array();
    }

    $reg_deadline = (int)$reg_deadline;
    $start = (int)$start;
    $end = (int)$end;
    
    $res = $collection->insertOne(
        [
          "name" => $name,
          "position" => $position,
          "deadline" => $reg_deadline,
          "start" => $start,
          "end" => $end,
          "type" => $type,
          "unit" => $unit,
          "unit_names" => $unit_names,
          "creation_date" => $tnow, 
          "options" => $options 
        ]
    );
    $c = $res->getInsertedCount();
    return $c;

   }//end elections fcuntion


   function get_user_code($election_id, $fname, $oname, $sname, $bmonth, $byear, $secret){

    $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $collection = $client->$x->code_db; //Select collection

    //gen user hash 
    $gen = new key_mgt;
    $user_id = $gen->gen_user_hash($fname, $oname, $sname, $bmonth, $byear, $secret);

    //get code from db

    $res = $collection->findOne(
        [
            "user_hash" => $user_id,
            "election_id" => $election_id
            ]
    );
    if(isset($res->code)){
        $code = $res->code;
    } else {
        $code = "No code found";
    }
    return $code;
   } //end function

   private function get_user_pvkey($election_id, $user_id, $code){
    $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $collection = $client->$x->code_db; //Select collection

    $res = $collection->findOne(
        [
            "user_hash" => $user_id,
            "election_id" => $election_id,
            "code" => $code
            ]
    );

    if(isset($res->pv_key)){
        $pvkey = $res->pv_key;
        $pvkey = base64_decode($pvkey);
    } else {
        $pvkey = FALSE;
    }
    return $pvkey;
   } //end function
   
function get_election_pbkey($election_id){
    $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $collection = $client->$x->na_elections; //Select collection
    $election_id = strtolower($election_id);
    $res = $collection->findOne(
        [
            "name" => $election_id,
        ],
        [
            "projection" => [
                "pbkey" => 1
            ]
        ]
    );

    if(isset($res->pbkey)){
        $pbkey = $res->pbkey;
    } else {
        $pbkey = "No key found";
    }
    return $pbkey;
   }

   function get_election_details($election_id){
    $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $collection = $client->$x->na_elections; //Select collection

    $res = $collection->findOne(
        [
            "name" => $election_id
        ]
    );
    return $res;
   }

   function get_election_options($election_id){
    $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $collection = $client->$x->na_elections; //Select collection

    $res = $collection->findOne(
        [
            "name" => $election_id,
        ],
        [
            'projection' => [
                'options' => 1
            ]
        ]
    );
    return $res;
   }

 function encrypt_vote($vote_string, $election_id){
       $pbkey = $this->get_election_pbkey($election_id);
       $pbkey = base64_decode($pbkey);
       $ok= openssl_public_encrypt($vote_string,$encrypted,$pbkey);
      $enc = base64_encode($encrypted);
    return $enc;
   }

   private function sign_vote($vote_string, $pvkey){
    openssl_sign($vote_string, $signature, $pvkey);
    $signature = base64_encode($signature);
    return $signature;
   } 

   function cast_vote($election_id, $user_id, $code, $vote){

    //check that timing is correct
    //get current time
    $tm = new na_clean;
    $curr = $tm->new_time();

    //get election start and end times 
    $st = $this->get_election_start($election_id);
    $se = $this->get_election_end($election_id);

    if($curr < $st) {
        $std = gmdate("d-m-Y\TH:i:s\Z", $st);
        $message = "Election has not started. Please try again from $std AST";
        return $message;
        exit;
    }

    if($curr > $se){
        $message = "Voting period has ended";
        return $message;
        exit;
    }

    //1. get private keys
    $pvkey = $this->get_user_pvkey($election_id, $user_id, $code);
    if($pvkey==FALSE){
        $ret = "Your unique election code is wrong. Please check code and try again";
        return $ret;
        exit;
    }

    //2. set vote string 
    //get random 8 digit code to append to vote. This ensures votes cannot be counted before decryption.
    $k = new key_mgt;
    $nc = $k->gr_bytes();
    $vote_string = "$nc,$vote";

    //2b Generate hash of vote string
    $vshash = hash("sha512", $vote_string);

    //3. encrypt vote with election public key and base64 encode
    $enc_vote = $this->encrypt_vote($vote_string, $election_id);
    
    if ($enc_vote == ""){
        return "Something went wrong 1. Please try again. $election_id";
        exit;
    }
    //4. digitally sign vote hash with voter private key
    $signed_vote = $this->sign_vote($vshash, $pvkey);
    if($signed_vote == ""){
        return "Something went wrong 2. Please try again.";
        exit;
    }

    //get time stamp
    $curr_time = $curr;


    //verify vote with public key from voter roll
    $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $collection = $client->$x->{$election_id."_vroll"}; //Select collection
    
    $find = $collection->findOne(
        [
            'user_hash' => $user_id, 
         ],
         [
           'projection' => [
               'pb_key' => 1,
               'vote' => 1
           ]
         ]
        );
    $pbkey = $find->pb_key;
    $pbkey = base64_decode($pbkey);

    $ok = openssl_verify($vshash, base64_decode($signed_vote), $pbkey);

    if($ok === 0){
        return "Something went wrong 3. Please try again.";
        exit;
    }

    //check if user already voted
    if(isset($find->vote)){
        return "Vote already cast.";
        exit;
    }

    //enter vote into database if no error
    

    $updateResult = $collection->updateOne(
        ['user_hash' => $user_id],
        ['$set' => [
            'vote' => $enc_vote,
            'vshash' => $vshash,
            'signature' => $signed_vote,
            'time_stamp' => $curr_time,
            'block_hash' => 'NULL'
            ]
        ]
    );
    
    //if succesful enter, return true, else return false.
    $count = $updateResult->getModifiedCount();
    if ($count > 0){
        $message = "$count vote cast. You can verify your votes at any of the public registers.<br />
        Encrypted vote: $enc_vote. <br />
        Signed vote: $signed_vote. <br />
        Time voted: $curr_time. <br />
        ";
    } else {
        $message = "Something went wrong ins. Please try again";
    }
    return $message;

   } //end function



   function decrypt_votes($election_id, $privkey){

    //check if election is finished

    //get current time
    $td = new na_clean;
    $curr_time = $td->new_time();

    $se = $this->get_election_end($election_id);

    if ($curr < $se){
        $message = "Election is not over. Voting still going on. How did you get the private key? We are watching you";
        return $message;
        exit;
    }

    //get election end time from database
    $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $collection = $client->$x->na_elections; //Select collection
    
    $find = $collection->findOne(
        [
            'name' => $election_id, 
         ],
         [
           'projection' => [
               'end' => 1
           ]
         ]
        );
    $end = $find->end;

    //check if election has ended
    if($curr_time < $end ){
        //return "Elections have not ended";
        //exit;
        }

    //find all votes and decrypt with private key
    $privkey = base64_decode($privkey);

    $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $start = $client->$x->{$election_id."_vroll"}; //Select collection

    $vs = $start->find([

    ],
    [
        'projection' => [
            'vote' => 1,
            'user_hash' => 1,
        ]
    ]);
$var = 0;
    foreach ($vs as $x){
    
        $obj = $x->{'_id'};
        if (isset($x->vote)){
        $vote_string = $x->vote;
        $vote_string = base64_decode($vote_string);
        $user_hash = $x->user_hash;
        //decrypt vote 
       $ok = openssl_private_decrypt($vote_string,$decrypted,$privkey);
    
        if ($ok != FALSE){

            //update vote
    $upd = $this->update_vote($election_id, $decrypted, $user_hash, $obj);
    $var = $var+1;

        }
     } 
    }

    return $var;


   } //end function 

   private function update_vote($election_id, $vote_string, $user_id, $obj){
       $exp = explode(",", $vote_string);
       $vote = $exp[1];
       
       $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
        $x = $this->db_name;
        $collection = $client->$x->{$election_id."_vroll"}; //Select collection

        $updateResult = $collection->updateOne(
            ['_obj' => $obj, 'user_hash' => $user_id],
            ['$set' => [
                'decrypted_vote' => $vote
                ]
            ]
        );
        
        return TRUE;
       
   }

   function publish_results($election_id){
    $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $collection = $client->$x->{$election_id."_vroll"}; //Select collection
    $var = array();
    $pub = $collection->aggregate(
        [   
            ['$group' =>
             [
                 '_id' => 
                 [
                    "ward"=>'$ward',
                    "option"=>'$decrypted_vote'
                ],
                 'votes' => ['$sum' => 1]
             ]
            ],
            ['$sort' => ['_id' => 1]],
        ]
    );

    foreach ($pub as $x){
        if(!isset($x->{'_id'}->option)){
            $option = "Invalid votes";
        } else {
         $option = $x->{'_id'}->option;
        }
        $ward = $x->{'_id'}->ward;
        $votes = $x->votes;
        $res = array("Ward"=>$ward, "Option"=>$option, "votes"=>$votes);    
        $var[] = $res;
    }

    return $var;

   } //end function 

   function calculate_winner($election_id, $protocol){

        //check if decryption complete
        $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $collection = $client->$x->{$election_id."_vroll"}; //Select collection
    
    $res = $collection->findOne(
        [
            'decrypted_vote' => null,
            'vote' => ['$ne' => null]
        ]
        );

    if ($res != NULL){
        $message = "Vote decryption still on going. Please try again later";
        return $message;
        exit;
    }


        if($protocol == "straight"){
            $get = $this->straight_winner($election_id);
        } elseif($protocol == "ward"){
            $get = $this->ward_winner($election_id);
        } elseif($protocol == "lga"){
            $get = $this->lga_winner($election_id);
        } else{
            $get = "Invalid protocol";
        }

        return $get;
   }//end function

   private function straight_winner($election_id){
    $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $collection = $client->$x->{$election_id."_vroll"}; //Select collection
    $var = array();
    $pub = $collection->aggregate(
        [   
            [
                '$match' => [
                    'decrypted_vote' => ['$ne' => null]
                ]
            ],
            ['$group' =>
             [
                 '_id' => '$decrypted_vote',
                 'votes' => ['$sum' => 1]
             ]
            ],
            ['$sort' => ['_id' => 1]],
        ]
    );

    foreach ($pub as $x){
        if(!isset($x->{'_id'})){
            $option = "Invalid votes";
        } else {
         $option = $x->{'_id'};
        }
        $votes = $x->votes;
        $res = array("Option"=>$option, "votes"=>$votes);    
        $var[] = $res;
    }
    return $var;
   }//end funtion

   private function ward_winner($election_id){
    $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $collection = $client->$x->{$election_id."_vroll"}; //Select collection
    $var = array();
    $pub = $collection->aggregate(
        [   
            [
                '$match' => [
                    'decrypted_vote' => ['$ne' => null]
                ]
            ],
            ['$group' =>
             [
                 '_id' => 
                 [
                    "ward"=>'$ward',
                    "option2" => '$decrypted_vote',
                    "title" => ['$concat' => 
                                ['$ward', ' | ', '$decrypted_vote']
                        ],
                    "option"=>
                    [
                       '$ifNull' => ['$decrypted_vote', 'Invalid Votes' ] //{$ifNull: ['$A', '$B'] }}
                    ]
                ],
                 'votes' => ['$sum' => 1],
                 'cand' => ['$max' => '$decrypted_vote']
             ]
            ],
            /*
            [ 
                '$group' => 
                [
                    '_id' => 
                        [
                        "ward" => '$_id.title',
                        ],
                    'Winner' => ['$max' => '$votes']
                ]
            ],
            ['$sort' => ['Candidate' => 1]],
            */
        ]
    );
    $temp = array();
    foreach ($pub as $x){
        $b2 = $x->{'_id'};
        $ward = $b2["ward"];
        $candidate = $b2["option"];
        $votes = $x->votes;

        //check if the current ward exists in array.
        //if it doesn't insert
        //if it does compare votes.
        if(!isset($temp[$ward])){
            $temp[$ward] = array("ward"=>$ward,"ward_winner"=>$candidate, "ward_votes"=>$votes);
        } else {
            //get current winning candidate
            $wvotes = $temp[$ward]["ward_votes"];


            if($votes > $wvotes){
                //if new is greater than winner
                $temp[$ward] = array("ward"=>$ward, "ward_winner"=>$candidate, "ward_votes"=>$votes);
            } elseif ($votes == $wvotes){
                //if new is equal to winner.
                $ocand = $temp[$ward]["ward_winner"];
                $temp[$ward] = array("ward"=>$ward, "ward_winner"=>"$ocand and $candidate", "ward_votes"=>$votes);
            }
        }
    }

    return $temp;
   }//end funtion

   //winner based on winning LGA
   private function lga_winner($election_id){
    $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $collection = $client->$x->{$election_id."_vroll"}; //Select collection
    $var = array();
    $pub = $collection->aggregate(
        [   
            [
                '$match' => [
                    'decrypted_vote' => ['$ne' => null]
                ]
            ],
            ['$group' =>
             [
                 '_id' => 
                 [
                    "lga"=>'$lga',
                    "option2" => '$decrypted_vote',
                    "title" => ['$concat' => 
                                ['$lga', ' | ', '$decrypted_vote']
                        ],
                    "option"=>
                    [
                       '$ifNull' => ['$decrypted_vote', 'Invalid Votes' ] //{$ifNull: ['$A', '$B'] }}
                    ]
                ],
                 'votes' => ['$sum' => 1],
                 'cand' => ['$max' => '$decrypted_vote']
             ]
            ],
        ]
    );
    $temp = array();
    foreach ($pub as $x){
        $b2 = $x->{'_id'};
        $lga = $b2["lga"];
        $candidate = $b2["option"];
        $votes = $x->votes;

        //check if the current ward exists in array.
        //if it doesn't insert
        //if it does compare votes.
        if(!isset($temp[$lga])){
            $temp[$lga] = array("lga"=>$lga,"lga_winner"=>$candidate, "lga_votes"=>$votes);
        } else {
            //get current winning candidate
            $wvotes = $temp[$lga]["lga_votes"];


            if($votes > $wvotes){
                //if new is greater than winner
                $temp[$lga] = array("lga"=>$lga, "lga_winner"=>$candidate, "lga_votes"=>$votes);
            } elseif ($votes == $wvotes){
                //if new is equal to winner.
                $ocand = $temp[$lga]["lga_winner"];
                $temp[$lga] = array("lga"=>$lga, "lga_winner"=>"$ocand and $candidate", "lga_votes"=>$votes);
            }
        }
    }

    return $temp;
   }//end funtion

   function add_election_options($election_id, $options){
    $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $collection = $client->$x->na_elections; //Select collection

    //get current time
    $crs = new na_clean;
    $tnow = $crs->new_time();

    $ed = $this->get_election_deadline($election_id);

    if($tnow > $ed){
        $message = "Nomination deadline passed.";
        return $message;
        exit;
    }

    //update with options if time not expired.
    $res = $collection->updateOne(
        [
            'name' => "$election_id",
            'deadline' => ['$gt' => $tnow]
        ],
        [
            '$addToSet' => [
                'options' => ['$each' => $options]
            ]
        ]
    );

    $upd = $res->getModifiedCount();
    return $upd;   
   } //end function

   function get_elections(){
    //query db
    $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $collection = $client->$x->na_elections; //Select collection

    $res = $collection->find();
    $elections = array();
    foreach($res as $x){
        $elections[] = $x;
    }

    return $elections;
   } //end function

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

   private function get_election_start($election_id){
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
                'start' => 1
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

   private function get_election_end($election_id){
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
                'end' => 1
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

}
?>