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
 $exp = array("name", "position", "reg_deadline", "start", "end", "type", "unit", "unit_names", "auth_key");
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
$name = $data->name;
$position = $data->position;
$reg_deadline = $data->reg_deadline;
$start = $data->start;
$end = $data->end;
$type = $data->type;
$unit = $data->unit;
$unit_names = $data->unit_names;
$auth_key = $data->auth_key;

$clean = new na_clean;
$name = $clean->clean_str($name);
$position = $clean->clean_str2($position);
$reg_deadline = $clean->clean_str($reg_deadline);
$start = $clean->clean_str($start);
$end = $clean->clean_str($end);
$type = $clean->clean_str($type);
$unit = $clean->clean_str($unit);
$unit_names = $clean->clean_array2($unit_names);
$auth_key = $clean->clean_str($auth_key);

//validate Auth Key




//Query database
$query = new na_vote;
$result = $query->create_election($name, $position, $reg_deadline, $start, $end, $type, $unit, $unit_names);

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
