<br /><br />
<div class="row">
<h3> Election finished. Results</h3>
</div>
<div class="row">
<?php 
if(isset($name)){
    $e_results = $call->get_election_results($name);
    $full_results = $call->full_results($name);
    if(is_array($e_results)==FALSE){
        echo "$e_results";
        $e_results = array();
        $full_results = array();
    }
    foreach($e_results as $z){
        $option = $z->Option;
        $votes = $z->votes;
        echo "$option $votes<br /><br />";
    }
}

?>
</div>
<div class="row">
<h3> Winners by ward</h3>
</div>
<div class="row">
<?php 
foreach($full_results as $b){
    $ward = $b->Ward;
    $opt = $b->Option;
    $vot = $b->votes;
    echo '<div class="col-md-4">';
    echo "Ward: $ward<br />Option: $opt<br />Votes: $vot<br /><br /><br />";
    echo '</div>';
}
?>
</div>

<div class="row">
Load full election results database.
</div>