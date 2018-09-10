<?php
//custom page info
$page_title = "Nawo E-Voting Framework - Open Elections.";
include("../php_includes/header.php");
include("../php_includes/call_api.php");

//get current date.
date_default_timezone_set('Africa/Lagos');
$timestamp = time();
$currdate = gmdate("d M Y H:i:s", $timestamp);

//load elections
$call = new call_api;
$elections = $call->get_elections();
?>

<div class="container">
    <div class="row">
        <h3>Current Time: <?php echo $currdate; ?></h3>
    </div>
    <div class="row">
    <h3>Elections</h3>
    </div>
    <div class="row">
    <?php 
        foreach ($elections as $x){
            $name = $x->name;
            $position = $x->position;
            $deadline = gmdate("d M Y H:i:s", $x->deadline);
            $start = gmdate("d M Y H:i:s", $x->start);
            $end = gmdate("d M Y H:i:s", $x->end);
            $type = $x->type;
            $unit = $x->unit;
            $unit_names = $x->unit_names;
            $options = $x->options;
            echo '<div class="col-md-4 topbuffer">';
            echo "Election name: $name <br />
                Position: $position<br />
                Registration Deadline: $deadline <br />
                Election start time: $start<br />
                Election end time: $end <br />
                Type: $type<br />
                Election level: $unit<br />
                Open for the following ".$unit."s: ";
                foreach($unit_names as $y){
                    echo "$y, ";
                }
                echo "<br /> 
             <a href=\"./election.php?id=$name\">Go to election page</a><br /><br />";
            echo '</div>';
        }
    ?>
    </div> 
</div> <!-- end main big div -->


<?php 
include("../php_includes/footer.php");
?>