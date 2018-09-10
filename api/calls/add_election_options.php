<?php
/* 
* Check you party status
* Returns User hash, join/update date, Party Status and expiry dates with block hashes. 
* Requires Unique user hash
* 
*/

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
 $exp = array("election_id", "options", "auth_key");
 foreach ($exp as $value) {
   if (empty($data->$value)){
     $error_counter = $error_counter +1;
     $missing = $missing." $value";
   }
 }
 if($error_counter>0){
   echo error_response(400, "Incorrect parameters");
   exit();
 }

//get arguments
$election_id = $data->election_id;
$options = $data->options;
$auth_key = $data->auth_key;

$clean = new na_clean;
$election_id = $clean->clean_str($election_id);
$options = $clean->clean_array2($options);
$auth_key = $clean->clean_str($auth_key);

//validate Auth Key


//convert options to indexed array
$options2 = array();
foreach($options as $p){
  $options2[] = $p;
}
//Query database
$query = new na_vote;
$result = $query->add_election_options($election_id, $options2);

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
