<?php

/*
Class to clean and format variables
*/


class na_clean{

    // Removes special chars and spaces. all to lower case too
  function clean_str($str) {
    $str = preg_replace('/[^A-Za-z0-9\-]/', '', $str);
    $str = strtolower($str);
    return $str;
  } //end function

  //converts special characters to html format
  function clean_str3($str){
    $str = htmlspecialchars($str);
    return $str;
  }

  //removes special characters be leaves spaces. all to lower casee too.
  function clean_str2($str) {
    $str = preg_replace('/[^a-zA-Z0-9\s]/', '', $str);
    $str = strtolower($str);
    return $str;
  } //end function

  function clean_array($arr){
    $clarr = array();
    foreach($arr as $a){
      $a = $this->clean_str($a);
      $a = strtolower($a);
      $clarr[$a] = $a; 
    }
    return $clarr;
  } //end function

  //uses clean string 2
  function clean_array2($arr){
    $clarr = array();
    foreach($arr as $a){
      $a = $this->clean_str2($a);
      $a = strtolower($a);
      $clarr[$a] = $a; 
    }
    return $clarr;
  } //end function


  // Checks if email is valid. Returns true or false
  function val_email($email){
          //get the email to check up, clean it
          $email = filter_var($email,FILTER_SANITIZE_STRING);
          // 1 - check valid email format using RFC 822
          if (filter_var($email, FILTER_VALIDATE_EMAIL)===FALSE) {
              return 0;
              }
          else {
          return TRUE;
        }
      } //end function

// check if valid phone number
function val_mobile($no){
    $ret = TRUE;
    return $ret;
  }


 function val_month($mnt){
   if($mnt >= 0 & $mnt <= 12){
     return TRUE;
   } else {
    return FALSE;
    }
  } //end function
  
  function val_year($year){
    if($year >= 1900 & $mnt <= 2100){
      return TRUE;
    } else {
     return FALSE;
     }
   }

//Get current time stamp. Default timezone is Africa/Lagos
function new_time(){
    date_default_timezone_set('Africa/Lagos');
    $date = time();
    return $date;
  }
}

?>