<div class="container bigcont bpad">
    <div class="row">
        <h3>
            Cast Vote<br /><br />
        </h3>
    
      <form action="./vote.php" method="post" class="form-inline" name="join-party" id="join-party">
        <div class="form-group">  
    
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
<input form="join-party" class="form-control" type="hidden" name="election_id" id="election_id" value="<?php echo $name; ?>">
  Election code:  <input form="join-party" class="form-control" type="password" id="election_code" name="election_code" placeholder="Election Code" required></input> 
<br /> <br/>

        Select Option:
            <div id="state" class="form-control wheight">
            <select class="form-control" id="fvote" name="fvote" form="join-party" type="text" required >
                        <option disabled selected>Select option</option>
                        <?php
                        foreach($options as $x){
                            echo "<option value=\"$x\">$x</option>";
                        }
                        ?>
                    </select>
            </div>
        <br /><br />
        <button type="button" id="confirm" class="btn btn-primary" onclick="openConf()">Vote</button>

        <!--Confirm vote start -->                
        <div id="subForm" class="menuclass alcent">
        <div class="container">
        <div class="row topbuffer">
                        <h3>
                        Confirm Vote
                        </h3>
        </div>
        <div class="row topbuffer">
                        You are about to vote. Please confirm your choice. You cannot change your vote once it is submitted.
        </div>
        <div class="row topbuffer">
        First name: <span id="fname" class="tbold"></span>. Other name: <span id="oname" class="tbold"></span>. Surname: <span id="sname" class="tbold"></span>.
        </div>
        <div class="row topbuffer">
                        Birth month: <span id="bmonth" class="tbold"></span>. Birth year: <span id="byear" class="tbold"></span>.
        </div>
        <div class="row topbuffer">
                        Option: <span id="voption" class="tbold"></span>
        </div>
        <div class="row topbuffer">
        <button type="submit" id="submitbutton" class="btn btn-primary" form="join-party" onclick="closeConf()">Vote</button>
        <button type="button" class="btn btn-danger" onclick="closeConf()">Cancel</button>                
        </div>
        </div>
        
        </div>
        <!--confirm vote end-->
        
        </div>
    </form>
    </div>
</div>
<script>
    /* Open */
function openConf() {
    document.getElementById("fname").innerHTML = document.getElementById("first_name").value;
    document.getElementById("oname").innerHTML = document.getElementById("middle_name").value;
    document.getElementById("sname").innerHTML = document.getElementById("surname").value;
    document.getElementById("bmonth").innerHTML = document.getElementById("birth_month").value;
    document.getElementById("byear").innerHTML = document.getElementById("birth_year").value;
    document.getElementById("voption").innerHTML = document.getElementById("fvote").value;
    document.getElementById("subForm").style.height = "100%";
}
/* Close */
function closeConf() {
    document.getElementById("subForm").style.height = "0%";
} 
</script>
