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

$t = new na_vote;
$vs = "krgtsu,y8guhijthree";
$election_id = "man2018b";

$str = $t->get_election_deadline($election_id);

echo $str;

?>