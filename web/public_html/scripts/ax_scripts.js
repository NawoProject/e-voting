function get_lgas(str) {
    
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("innerLGA").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "get_lgas.php?q=" + str, true);
        xmlhttp.send();
}

function get_wards(str) {
    var pre = document.getElementById("fstate");
    var state = pre.options[pre.selectedIndex].value;
    
    var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("innerWard").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "get_wards.php?q=" + str + "&s=" + state, true);
        xmlhttp.send();
}

function check(input) {
    if (input.value != document.getElementById('sauce').value) {
        document.getElementById("ferror").innerHTML = '<div class="alert alert-danger" role="alert"><span class="xbold glyphicon glyphicon-exclamation-sign" aria-hidden="true"> Secret phrase must match!</span></div>';
        document.getElementById('submitbutton').disabled = true;
    } else {
        // input is valid -- reset the error message
        document.getElementById("ferror").innerHTML = '';
        document.getElementById('submitbutton').disabled = false;
    }
}