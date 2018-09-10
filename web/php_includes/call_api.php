<?php


class call_api{

    //attributes
    private $api_key;
    private $apilink2;

    function __construct(){
        require_once('../config/na_config.php'); //require configuration file
        $this->api_key = $api_key;
        $this->apilink2 = $apilink2;
      }


    private function call_api($call, $data){
        //set headers
$headers = array(
    'na_apikey: '.$this->api_key.''
);

// Get cURL resource
        $curl = curl_init();
// Set some options - we are passing in a useragent too here
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => "$this->apilink2/$call",
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

    //Get elections
    function get_elections(){
        $data = array();
        $data = json_encode($data);
        $call = "get_upcoming_elections";
        $result = $this->call_api($call, $data);
        //$states = $states->message;
        if ($result->status==200){
            return $result->details;
        } else {
            $error = "Error loading elections";
            return $error;
        } 
    }

    //Get elections
    function get_election_details($name){
        $data = array("election_id" => $name);
        $data = json_encode($data);
        $call = "get_election_details";
        $result = $this->call_api($call, $data);
        //$states = $states->message;
        if ($result->status==200){
            return $result->details;
        } else {
            $error = "Error loading election details";
            return $error;
        } 
    }

    function get_election_results($name){
        $data = array("election_id" => $name);
        $data = json_encode($data);
        $call = "get_winner";
        $result = $this->call_api($call, $data);
        //$states = $states->message;
        if ($result->status==200){
            return $result->details;
        } else {
            $error = "Error loading election results";
            return $error;
        }
    }

    function full_results($name){
        $data = array("election_id" => $name);
        $data = json_encode($data);
        $call = "get_full_results";
        $result = $this->call_api($call, $data);
        //$states = $states->message;
        if ($result->status==200){
            return $result->details;
        } else {
            $error = "Error loading election results";
            return $error;
        }
    }

    //get election code
    function get_election_code($data){
        $data = json_encode($data);
        $call = "get_user_code";
        $result = $this->call_api($call, $data);
        //$states = $states->message;
        if ($result->status==200){
            return $result->details;
        } else {
            $error = "Error loading Unique election code";
            return $error;
        }
    }

    //generate userid
    function gen_user_id($fname,$mname,$lname,$bmonth,$byear,$stoken){
        $salt = hash("sha512", $stoken);
$cost = 10;
$param='$'.implode('$',array(
            "2y", //select the most secure version of blowfish (>=PHP 5.3.7)
            str_pad($cost,2,"0",STR_PAD_LEFT), //add the cost in two digits
            $salt //add the salt
        ));
$combine = "$fname.$mname.$lname.$bmonth.$byear.$stoken";
$hash = hash("sha512", $combine);
$phash = crypt($hash, $param);
$fhash = hash("sha512", $phash); 
return $fhash;
    }

    //cast vote
    function castvote($data){
        $data = json_encode($data);
        $call = "cast_vote";
        $result = $this->call_api($call, $data);
        //$states = $states->message;
        if ($result->status==200){
            return $result->details;
        } else {
            $error = "Error loading election results";
            return $error;
        }
    }


    //Get unique states in wards database
    function get_states(){
        $data = array();
        $data = json_encode($data);
        $call = "get_states";
        $states = $this->call_api($call, $data);
        //$states = $states->message;
        if ($states->status==200){
            return $states->message;
        } else {
            $error = "Error loading states";
            return $error;
        }
    } //end getstates

    //Get unique lgas given states in wards database
    function get_lgas($state){
        $data = array("state"=>$state);
        $data = json_encode($data);
        $call = "get_lgas";
        $lgas = $this->call_api($call, $data);

        //$states = $states->message;
        if ($lgas->status==200){
            return $lgas->message;
        } else {
            $error = "Error loading LGAs";
            return $error;
        }
    } //end get_lgas

    //Get unique wards given state and lga in wards database
    function get_wards($state, $lga){
        $data = array("state"=>$state, "lga"=>$lga);
        $data = json_encode($data);
        $call = "get_wards";
        $wards = $this->call_api($call, $data);

        //$states = $states->message;
        if ($wards->status==200){
            return $wards->message;
        } else {
            $error = "Error loading Wards";
            return $error;
        }
    } //end get_lgas

}//end class

?>
