<?php
$start = microtime(true);
ini_set('max_execution_time', 1800); //1800 seconds = 30 minutes
ob_start(); 


include('../config/na_config.php');
include('../classes/init.php');
include('../classes/key_mgt.php');
include('../classes/wards.php');
include('../classes/clean.php');
include('../classes/file_save.php');
include('../classes/vote.php');
include('../classes/public_api.php');
include('../classes/crons.php');


$apilink = "http://localhost/party-register/api/public_html";
$apilink2 = "http://localhost/e-voting/api/public_html";

//functions 
function call_api($call, $data, $apil){
    //set headers
$headers = array(
'na_apikey: 6ff0376169c0cf89f2e98e53769fe5d6b8f291a85178e9bdad'
);

// Get cURL resource
    $curl = curl_init();
// Set some options - we are passing in a useragent too here
curl_setopt_array($curl, array(
CURLOPT_RETURNTRANSFER => 1,
CURLOPT_URL => "$apil/$call",
CURLOPT_USERAGENT => 'Sample Nawo web client',
CURLOPT_POST => 1,
CURLOPT_HTTPHEADER => $headers,
CURLOPT_POSTFIELDS => array(
    "data" => $data
)
));
// Send the request & save response to $resp
$resp = curl_exec($curl);
// Close request to clear up some resources
curl_close($curl);
$obj = json_decode($resp);
return $obj;
} //end call_api


// create random users ten users.
$user1 = array("fname" => "Peter", "oname" => "H", "sname"=> "Cech", "bmonth"=>"July", "byear"=>"1975", "ward"=>"ampang west", "lga"=> "mangu", "state"=> "plateau", "secret"=>"123abc456def");
$user2 = array("fname" => "Nacho", "oname" => "C", "sname"=> "Monreal", "bmonth"=>"July", "byear"=>"1975", "ward"=>"ampang west", "lga"=> "mangu", "state"=> "plateau", "secret"=>"123abc456def");
$user3 = array("fname" => "Hector", "oname" => "", "sname"=> "Bellerin", "bmonth"=>"August", "byear"=>"1985", "ward"=>"chanso", "lga"=> "mangu", "state"=> "plateau", "secret"=>"123abc456def");
$user4 = array("fname" => "Patrick", "oname" => "Wen", "sname"=> "Viera", "bmonth"=>"January", "byear"=>"1984", "ward"=>"kalong", "lga"=> "shendam", "state"=> "plateau", "secret"=>"123abc456def");
$user5 = array("fname" => "Laurent", "oname" => "L", "sname"=> "Kolscieny", "bmonth"=>"June", "byear"=>"1965", "ward"=>"kalong", "lga"=> "shendam", "state"=> "plateau", "secret"=>"123abc456def");
$user6 = array("fname" => "Rob", "oname" => "England", "sname"=> "Holding", "bmonth"=>"February", "byear"=>"1976", "ward"=>"kalong", "lga"=> "shendam", "state"=> "plateau", "secret"=>"123abc456def");
$user7 = array("fname" => "Henrick", "oname" => "", "sname"=> "Mkhitaryan", "bmonth"=>"December", "byear"=>"1994", "ward"=>"kalong", "lga"=> "shendam", "state"=> "plateau", "secret"=>"123abc456def");
$user8 = array("fname" => "Aaron", "oname" => "C", "sname"=> "Ramsey", "bmonth"=>"June", "byear"=>"1991", "ward"=>"kalong", "lga"=> "shendam", "state"=> "plateau", "secret"=>"123abc456def");
$user9 = array("fname" => "Pierre", "oname" => "Emerick", "sname"=> "Aubameyang", "bmonth"=>"November", "byear"=>"1990", "ward"=>"kalong", "lga"=> "shendam", "state"=> "plateau", "secret"=>"123abc456def");
$user10 = array("fname" => "Mesut", "oname" => "G", "sname"=> "Ozil", "bmonth"=>"October", "byear"=>"1961", "ward"=>"kalong", "lga"=> "shendam", "state"=> "plateau", "secret"=>"123abc456def");

$new_users = array($user1, $user2, $user3, $user4, $user5, $user6, $user7, $user8, $user9, $user10);

//register users
/*
$reg_sults = array();
foreach ($new_users as $x){
    $data = array("first_name"=>$x["fname"], "middle_name"=>$x["oname"], "surname"=>$x["sname"], "birth_month"=>$x["bmonth"], "birth_year"=>$x["byear"], "sauce"=>$x["secret"], "ward"=>$x["ward"], "lga"=>$x["lga"], "state"=>$x["state"]);
    $data = json_encode($data);
   
    $ins = call_api("join_party", $data, $apilink);
    $reg_sults[] = $ins;
}
print_r($reg_sults);
*/

// create election
/*
$name = "Man2018b";
$position = "Manager of Arsenal FC";

$day = 29;
$month = 07;
$year = 2018;
$dhour = 8;
$dmin = 00;
$shour = 8;
$smin = 10;
$ehour = 8;
$emin = 30;

date_default_timezone_set('Africa/Lagos');
   
$reg_deadline = strtotime("$year-$month-$day $dhour:$dmin:00" );
$start = strtotime("$year-$month-$day $shour:$smin:00" );
$end = strtotime("$year-$month-$day $ehour:$emin:00" );
$type = "election";
$unit = "state";
$unit_names = array("plateau");
$auth_key = "1234567";

$data = array("name"=>$name, "position"=>$position, "reg_deadline"=>$reg_deadline, "start"=>$start, "end"=>$end, "type"=>$type, "unit"=>$unit, "unit_names"=>$unit_names, "auth_key"=>$auth_key);
$data = json_encode($data);

$cr_elec = call_api("create_election", $data, $apilink2);
print_r($cr_elec);
exit;

*/
//add election options
/*
$election_id = "Man2018b";
$options = array("Arsene Wenger", "Unai Emery", "Tomas Tutchel", "Jose Mourinho", "Carlo Ancelotti", "Zinedine Zidane");
$option2 = array("Carlo Ancelotti", "Zinedine Zidane");
$auth_key = "1234567";

$data = array("election_id"=>$election_id, "options"=>$options, "auth_key"=>$auth_key);
$data = json_encode($data);

$add_elec = call_api("add_election_options", $data, $apilink2);
print_r($add_elec);
exit;
*/

// initialize election
/*
$election_id = "Man2018b";
$auth_key = "1234567";
$data = array("election_id"=>$election_id, "auth_key"=>$auth_key);
$data = json_encode($data);

$init_elec = call_api("initialize_election", $data, $apilink2);
print_r($init_elec);
exit;
*/

// cast votes for each user
/*
//get election options
$election_id = "Man2018b";
$data = array("election_id"=>$election_id);
$data = json_encode($data);

$elec_options = call_api("get_election_options", $data, $apilink2);
$options = $elec_options->details->options;

//cast votes
$vcast = array();
$gen = new key_mgt;
foreach ($new_users as $x){
    //get election code
    $fname = $x["fname"];
    $oname = $x["oname"];
    $sname = $x["sname"];
    $bmonth = $x["bmonth"];
    $byear = $x["byear"];
    $secret = $x["secret"];
    $data = array("election_id"=>$election_id, "fname"=>$fname, "oname"=>$oname, "sname"=>$sname, "bmonth"=>$bmonth, "byear"=>$byear, "secret"=>$secret );
    $data = json_encode($data);
    $cd = call_api("get_user_code", $data, $apilink2);
    $code = $cd->details;
    //generate election id
    $userid = $gen->gen_user_hash($fname, $oname, $sname, $bmonth, $byear, $secret);

    //select random vote
    $min = 0;
    $max = count($options);
    $max = $max-1;
    $choice = rand($min,$max);
     
    $data = array("election_id"=>$election_id, "user_id"=>$userid, "code"=>$code, "vote"=>$options[$choice]);
    $data = json_encode($data);

    $vote = call_api("cast_vote", $data, $apilink2);
    $vcast[] = $vote;
}

print_r($vcast);
exit;
*/

// decrypt votes
/*
$election_id = "man2018b";
$dec = new na_vote;
$privkey = "LS0tLS1CRUdJTiBQUklWQVRFIEtFWS0tLS0tCk1JSUJWQUlCQURBTkJna3Foa2lHOXcwQkFRRUZBQVNDQVQ0d2dnRTZBZ0VBQWtFQXZjVTI1UU9FMkpFbW0rc1YKOWd4bDJVanlZMkJsSHY2ZFdOMkZyNG42bjBucjZxcEN0c2gweEp3K2YySFo2eDNIS1dUTzN4eS9kc0k1a2hPLwp1eW02UHdJREFRQUJBa0J1TlJZSU1kNzRsbk5Cb1drRGwzZWVsSXFQdys5MTdKQWNoNm1YcEIzMFdzMXNmN2lWCktWQ09hTEljc3RSU0xsWTR5cjl5a2ZtZ21GUC83N1ZLZlJHaEFpRUE1c0NsOGpZWit5TDFxSFRnamdybmhPMG0KVmpmVGFGY1ZYa2psNm9WRzcwMENJUURTaUtrWmdNMmxkaHcxOUUxYzlDRnVmKzRodHdRcW9mWmlOa3MwYVlBaAp1d0lnTDQ2MjVPT1htVFhNVVlxOUdTbFFMQXBBWTNhZ0FKb3FFa09OOXphK3R1VUNJUUNWSEtldjRYTkZtcGEyCmZCYURISUhGTXFTbGltdFFDckJudFE2a3k0Z3Qyd0lnUm13ZkdrdXdlTUlTTE1nQjZLd2M2SzNFVExnRnp6eHoKd0E0VUFUMzFaK009Ci0tLS0tRU5EIFBSSVZBVEUgS0VZLS0tLS0K";
$do = $dec->decrypt_votes($election_id, $privkey);

print_r($do);
*/

//publish results

$election_id = "man2018b";
$protocol = "lga";
$cl = new na_vote;
$results = $cl->publish_results($election_id);
$winner = $cl->calculate_winner($election_id, $protocol);
echo "<b>Votes by LGA</b><br /><br />";

echo "<br /><br />";
foreach($results as $w){
    $ward = $w["Ward"];
    $option = $w["Option"];
    $votes = $w["votes"];
    echo "Ward: $ward. Winner: $option. Number of votes: $votes<br />";
}
echo "<br /><br /><br /><b>Ward Winners</b><br />";

foreach($winner as $q){
    $lga = $q["lga"];
    $option = $q["lga_winner"];
    $votes = $q["lga_votes"];
    echo "$lga: Winner $option with $votes <br />";
}

echo "<br /><br /><br /><b>Overall Winners</b><br />";

?>