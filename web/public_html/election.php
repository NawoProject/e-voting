<?php
//custom page info
$page_title = "Nawo E-Voting Framework - Open Elections.";
include("../php_includes/header.php");
include("../php_includes/call_api.php");

//get current date.
date_default_timezone_set('Africa/Lagos');
$timestamp = time();
$currdate = gmdate("d M Y H:i:s", $timestamp);

//load election details
if(isset($_GET['id'])){
    $name = htmlspecialchars($_GET['id']);
    $call = new call_api;
    $election = $call->get_election_details($name);
    $position = $election->position;
    $deadline = gmdate("d M Y H:i:s", $election->deadline);
            $start = gmdate("d M Y H:i:s", $election->start);
            $end = gmdate("d M Y H:i:s", $election->end);
            $type = $election->type;
            $unit = $election->unit;
            $unit_names = $election->unit_names;
            $options = $election->options;
          
} else {
    $name = "";
}
?>

<div class="container">
    <div class="row">
        <h3>Election for <?php echo $position; ?> [<?php echo $name; ?>]</h3>
    </div>
    <div class="row">
        Details:
    </div>
    <div class="row">
        Election nomination and registration deadline: <?php echo $deadline; ?><br />
        Election start date: <?php echo $start; ?><br />
        Election end date: <?php echo $end; ?><br /><br /><br />

        Type: <?php echo $type; ?><br />
        Election category: <?php echo $unit; ?><br />
        Allowed <?php echo $unit; ?>: 
        <?php 
            if($unit == "national"){
                $unit_names = array("ALL");
            }
            foreach ($unit_names as $y){
                echo "$y, ";
            }
            echo "<br />";
        ?>

        Options as at <?php echo $currdate ?>:<br />
        <?php
        $a = 1;
        foreach($options as $z){
            echo "$a. $z<br />";
            $a=$a+1;
        }
        ?>
    </div>
    <div class="row spacer">
    </div>
    <div class="row">
    <?php
        //if before deadline 
        if($timestamp < $election->deadline){
            include('../php_includes/preelection.php');
        }
        //if after deadline but before start
        if ($timestamp > $election->deadline & $timestamp < $election->start){
            include('../php_includes/preelection.php');
        }
        //if after start but before end
        if($timestamp>$election->start & $timestamp<$election->end){
            include('../php_includes/castvote.php');
        }
        //if election over show results
        if($timestamp > $election->end){
            include('../php_includes/results.php');
        }
    ?>
    </div>
    <br /><br /><br />
</div>


<?php 
include("../php_includes/footer.php");
?>