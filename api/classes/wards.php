<?php
/*
* Class for fetching ward details
*/


class na_wards{

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
   }


   function get_states(){
     $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
     $x = $this->db_name;
     $collection = $client->$x->na_wards; //Select collection

     try {
     $findResult = $collection->distinct("state");
     $ret = array(TRUE, $findResult);

    } catch(\Exception $e){
        $x1 = $e->getMessage();
        $ret = array( FALSE, $x1);
   }
   return $ret;
 }


 function get_lgas($state){
   $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
   $x = $this->db_name;
   $collection = $client->$x->na_wards; //Select collection

   try {
   $findResult = $collection->distinct("lga",
       [ "state" => "$state" ],
       [
         'sort' => ['lga' => +1],
        ]
      );
   $ret = array(TRUE, $findResult);

  } catch(\Exception $e){
      $x1 = $e->getMessage();
      $ret = array( FALSE, $x1);
 }
 return $ret;
}


function get_wards($state, $lga){
  $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
  $x = $this->db_name;
  $collection = $client->$x->na_wards; //Select collection

  try {
  //$findResult = $collection->distinct("ward", [ "state" => "$state", "lga" => "$lga" ]);
  $cursor = $collection->find(
    [
      'state' => $state, 'lga' => $lga
    ],
    [
      'projection' => [
        'ward' => 1,
        'wardcode' => 1
      ],
      'sort' => ['ward' => +1],
    ]
 );
 $temp = array();
 foreach ($cursor as $ward) {
   $temp [] = $ward;
 }

  $ret = array(TRUE, $temp);

 } catch(\Exception $e){
     $x1 = $e->getMessage();
     $ret = array( FALSE, $x1);
}
return $ret;
}


    function get_all_wards(){
        $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
        $x = $this->db_name;
        $collection = $client->$x->na_wards; //Select collection
      
        try {
        //$findResult = $collection->distinct("ward", [ "state" => "$state", "lga" => "$lga" ]);
        $cursor = $collection->find(
          [
            
          ],
          [
            'projection' => [
              'ward' => 1,
              'wardcode' => 1
            ],
            'sort' => ['ward' => +1],
          ]
       );
       $temp = array();
       foreach ($cursor as $ward) {
         $temp [] = $ward->ward;
       }
      
        $ret = $temp;
      
       } catch(\Exception $e){
           $x1 = $e->getMessage();
           $ret = array( FALSE, $x1);
      }
      return $ret;
    } // end function 

   function search_ward($ward){


       //create interest
       $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
       $x = $this->db_name;
       $collection = $client->$x->na_wards; //Select collection

       try {
       $findResult = $collection->find(
         ['ward' => ['$regex' => "$ward", '$options'=> 'i'] ],
         ['projection' => [
           'state' => 1,
           'lga' => 1,
           'ward' => 1,
           'wardcode' => 1
           ]]
       );
       $int_array = array();
       foreach ($findResult as $value) {

         $state = $value->state;
         $lga = $value->lga;
         $ward = $value->ward;
         $wardcode = $value->wardcode;
         $temp1 = array(
           "state" => $state,
           "lga" => $lga,
           "ward" => $ward,
           "wardcode" => $wardcode
         );
         $int_array[] = $temp1;
       }

       $ret = array(TRUE, $int_array);

      } catch(\Exception $e){
          $x1 = $e->getMessage();
          $ret = array( FALSE, $x1);
     }
     return $ret;
   }//end function

   //clean wards database
   function start_clean(){
    $client = new MongoDB\Client("mongodb://$this->db_server/$this->db_name", array( "username" => $this->db_username, "password" => $this->db_pass)); //connect to database
    $x = $this->db_name;
    $collection = $client->$x->na_wards; //Select collection

    $cl = new na_clean;
    $query = $collection->find();
    $w = array();
    foreach($query as $x){
      $w[] = $x;
    }
    $c= 0;  
    foreach ($w as $y){
      $obj = $y->{'_id'};
      $state = $cl->clean_str2($y->state);
      $lga = $cl->clean_str2($y->lga);
      $ward = $cl->clean_str2($y->ward);
      
      $updateResult = $collection->updateOne(
        ['_id' => $obj],
        ['$set' => [
          'state' => $state,
          'lga' => $lga,
          'ward' => $ward
            ]
          ]
      );
      $c = $c + $updateResult->getModifiedCount();  
    }
    return $c;
   }//end function


} //End class

?>
