<?php 

//custom page info
$page_title = "Nawo E-Voting Framework - Open Elections.";
include("../php_includes/header.php");
include("../php_includes/call_api.php");


//Get all variables
$fname = $_POST["first_name"];
$oname = $_POST["middle_name"];
$sname = $_POST["surname"];
$bmonth = $_POST["birth_month"];
$byear = $_POST["birth_year"];
$sauce = $_POST["sauce"];
$ecode = $_POST["election_code"];
$fvote = $_POST["fvote"];
$election_id = $_POST["election_id"];

//generate userid
$call = new call_api;
$user_id = $call->gen_user_id($fname,$oname,$sname,$bmonth,$byear,$sauce);        

//send vote to API
$data = array("election_id"=>$election_id, "user_id"=>$user_id, "code"=>$ecode, "vote"=>$fvote);
$castvote = $call->castvote($data);
?>
<div class="container">
    <div class="row">
<h3>Result [<?php echo $election_id; ?>]</h3>
    </div>
    <div class="row topbuffer">
<span>
Thank you <?php echo $fname; ?>.<br />
User: <?php echo $user_id; ?>
</span>
    </div>
    <div class="row topbuffer">
<span>
<h4>
<?php print_r($castvote); ?>
</h4>
</span>
    </div>
</div>

<?php 
include("../php_includes/footer.php");
?>