<?php 

//custom page info
$page_title = "Nawo E-Voting Framework - Open Elections.";
include("../php_includes/header.php");
include("../php_includes/call_api.php");


//Get all variables if form submitted
if(isset($_POST["first_name"])){
    $fname = $_POST["first_name"];
$oname = $_POST["middle_name"];
$sname = $_POST["surname"];
$bmonth = $_POST["birth_month"];
$byear = $_POST["birth_year"];
$sauce = $_POST["sauce"];
$election_id = $_POST["election_id"];

$call = new call_api;

//Get election code
$data = array("election_id"=>$election_id, "fname"=>$fname, "oname"=>$oname, "sname"=>$sname, "bmonth"=>$bmonth, "byear"=>$byear, "secret"=>$sauce);
$ecode = $call->get_election_code($data);

}
?>
<div class="container">
    <div class="row">
<h3>Get election code</h3>
    </div>

    <div class="row topbuffer btn-danger wmargin">
    For security reasons your unique election code will typically only be sent to the registered phone number or email address, and may alternatively
    only be accessible at ward offices. But this is a demo so .......
    </div>

    <div class="row topbuffer">
    <div class="container bigcont bpad">
    <div class="row">
        
      <form action="./election_code.php" method="post" class="form-inline" name="join-party" id="join-party">
        <div class="form-group">
        Election ID: <input form="join-party" class="form-control" type="text" id="election_id" name="election_id" placeholder="Election ID" required></input> 
        <br /><br />
         Names:  <input form="join-party" class="form-control" type="text" id="first_name" name="first_name" placeholder="First Name" required></input>
         <input form="join-party" class="form-control" type="text" id="middle_name" name="middle_name" placeholder="Middle Name"></input>
         <input form="join-party" class="form-control" type="text" id="surname" name="surname" placeholder="Surname" required></input><br /><br />
        Date of Birth: <select form="join-party" class="form-control" type="text" id="birth_month" name="birth_month" required>
                        <option disabled selected>Month</option>
                        <option value="January">January</option>
                        <option value="Febrary">February</option>
                        <option value="March">March</option>
                        <option value="April">April</option>
                        <option value="May">May</option>
                        <option value="June">June</option>
                        <option value="July">July</option>
                        <option value="August">August</option>
                        <option value="September">September</option>
                        <option value="October">October</option>
                        <option value="November">November</option>
                        <option value="December">December</option>
            </select>
            <select form="join-party" class="form-control" type="text" id="birth_year" name="birth_year" required>
                <option disabled selected>Year</option>
                <?php
                date_default_timezone_set('Africa/Lagos');
                $year = date("Y");
                $year = $year - 15;
                $sdate = $year - 100;
                while ($sdate != $year) {
                    echo "<option value=\"$sdate\">$sdate</option>";
                    $sdate = $sdate+1;
                } 
                ?>
            </select>

                    <br /><br />

        
Secret phrase:  <input form="join-party" class="form-control" type="password" id="sauce" name="sauce" placeholder="Secret" required></input> 
<br /> <br/>

        <button type="submit" id="submitbutton" class="btn btn-primary" form="join-party">Get Unique Election Code</button>
                
        </div>
    </form>
    </div>
</div>

    </div>

    <div class="row topbuffer">
<span><h4>
Election ID: <?php if(isset($election_id)){echo $election_id;} ?><br /><br />
Your election code is: <?php 
    if(isset($ecode)){
        print_r($ecode); 
        }
?></h4>
</span>
    </div>
    <div class="row topbuffer">
    </div>
</div>

<?php 
include("../php_includes/footer.php");
?>