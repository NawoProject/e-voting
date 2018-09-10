<?php
/* 
* Check you party status
* Returns User hash, join/update date, Party Status and expiry dates with block hashes. 
* Requires Unique user hash
* 
*/

//extend script running time
ini_set('max_execution_time', 86400); //300 seconds = 5 minutes

$method = "POST";


// Check if expected json sent
  if(!isset($_POST["data"])){
    echo error_response(400, 'Bad request');
    exit();
  } else {
  $data = json_decode($_POST['data']);
 }

 // check if required arguments sent, extract and clean, except event_orgs array
 //error counter
 $error_counter = 0;
 $missing = "";
 $exp = array("election_id", "auth_key");
 foreach ($exp as $value) {
   if (empty($data->$value)){
     $error_counter = $error_counter +1;
     $missing = $missing." $value";
   }
 }
 if($error_counter>0){
   echo error_response(400, "Incorrect parameters $missing");
   exit();
 }

//get arguments
$name = $data->election_id;
$auth_key = $data->auth_key;

$clean = new na_clean;
$name = $clean->clean_str($name);
$auth_key = $clean->clean_str($auth_key);

//validate Auth Key




//Query database
$query = new na_init;
$result = $query->gen_voter_roll($name);

if($result!=FALSE){
header('Status: 200 OK');
$return = json_encode(array(
    'status' => 200, // success or not?
    'message' => 'OK',
    'details' => $result
    ));
print_r($return);
} else {
echo error_response(400, "No results");
}

?>
