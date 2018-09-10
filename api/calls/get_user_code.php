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

 $exp = array("election_id", "fname", "sname", "bmonth", "byear", "secret");
 foreach ($exp as $value) {
   if (empty($data->$value)){
     $error_counter = $error_counter +1;
   }
 }
 if($error_counter>0){
   echo error_response(400, 'Incorrect parameters');
   exit();
 }

//get arguments
$election_id = $data->election_id;
$fname = $data->fname;
if(isset($data->oname)){
  $oname = $data->oname;
} else {
  $oname = "";
}
$sname = $data->sname;
$bmonth = $data->bmonth;
$byear = $data->byear;
$secret = $data->secret;

$clean = new na_clean;
$election_id = $clean->clean_str($election_id);
$fname = $clean->clean_str2($fname);
$oname = $clean->clean_str2($oname);
$sname = $clean->clean_str2($sname);
$bmonth = $clean->clean_str($bmonth);
$byear = $clean->clean_str($byear);
$secret = $clean->clean_str($secret);


//Query database
$query = new na_vote;
$result = $query->get_user_code($election_id, $fname, $oname, $sname, $bmonth, $byear, $secret);

if($result!=FALSE){
header('Status: 200 OK');
$return = json_encode(array(
    'status' => 200, // success or not?
    'message' => "OK",
    'details' => $result
    ));
print_r($return);
} else {
echo error_response(400, "No results");
}

?>
