<?php 

include('../classes/clean.php');
include('../classes/api_auth.php');
include('../php_includes/error_handle.php');
include('../classes/wards.php');



//set up databases. Create unique indexes for elections db and api access db
$start = new api_auth;
$create = $start->create_index();
print_r($create);
?>