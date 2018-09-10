<?php
/* API public endpoint
* @author = Nonso Obikili
*/

// include required files
include('../classes/clean.php');
include('../classes/api_auth.php');
include('../classes/key_mgt.php');
include('../classes/file_save.php');
include('../php_includes/error_handle.php');
include('../classes/init.php');
include('../classes/vote.php');
include('../classes/wards.php');


//Get custom headers
header('Access-Control-Allow-Headers: na_apikey');
$header = getallheaders();


// Validate client has valid API key
$clean = new na_clean;
if(!isset($header["na_apikey"])){
  echo error_response(401, 'Unauthorized. If you need an api key please visit nawo.ng/getapikey. Please see github repo for API Documentaion');
  exit();
} else {
  $apikey = $clean->clean_str($header["na_apikey"]);
}
$val_api = new api_auth;
if ($val_api->val_api($apikey) == FALSE){ // if key is not valid
  echo error_response(401, 'Unauthorized. Invlaid API key');
  exit();
} else { //Load call

  if (isset($_REQUEST["call"])){  //If call is sent -- Still need to sort out to use .htaccess
    $call = $clean->clean_str3($_REQUEST["call"]);
    if(file_exists("../calls/$call.php")){
    include("../calls/$call.php");
    } else {    //return bad call
      echo error_response(404, 'Call not found');
    }
  } else { //call not set. load error
    echo error_response(400, 'Bad Request');
  }

}
?>
