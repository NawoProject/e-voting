<?php
//cron task that runs every so minutes to process blocks ordered by timestamp.

// include required files
include('../classes/clean.php');
include('../classes/crons.php');
include('../classes/api_auth.php');
include('../classes/vote.php');
include('../php_includes/error_handle.php');
include('../classes/wards.php');


//extend processing time
ini_set('max_execution_time', 1800); //1800 seconds = 30 minutes

//get election ID if set
if(!isset($_GET['election_id'])){
    echo "Invalid parameters";
    exit;
} else {
    $election_id = $_GET['election_id'];
}

$clean = new na_clean;
$election_id = $clean->clean_str($election_id);


//Get next 100 unprocessed blocks
$votes = new na_crons;
$blocks = $votes->load_unp_votes($election_id);


//get last processed hash
$x = 0;
$y = count($blocks);

while ($x < $y){
    $bl_array = $blocks["$x"];

    $obj_id = $bl_array->{'_id'};
    $obj_id = (array)$obj_id;
    $obj_id = $obj_id['oid'];
    
    $user_hash = $bl_array->user_hash;
    $vote = $bl_array->vote;
    $time_stamp = $bl_array->time_stamp;
    
    //get last hash
    $last_hash = $votes->get_last_block_hash($election_id);    
    $lhash = $last_hash->block_hash;
    
    //gen new hash
    $hstring = "$lhash.$user_hash.$vote.$time_stamp";
    $new_hash = hash('sha256', $hstring);
    

    //update user db
    $update = $votes->update_next_user($election_id, $obj_id, $new_hash);

    //wait 1 seconds 
    sleep(1);

    
    $wait = 0;
    while($wait != 1){
        $test_hash = $votes->get_last_block_hash($election_id)->block_hash;

        if($test_hash == $new_hash){
            // wait one more second. Keep waiting until finish
            sleep(1);
        } else {
        $wait = 1;
        $x = $x+1;
        }
    } // end internal while loop
 echo " New hash: $new_hash <br />";

} // end while loop

?>