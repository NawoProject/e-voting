<?php 


class key_mgt{

    function get_new_keys(){
        $config = array(
            "digest_alg" => "ripemd160",
            "private_key_bits" => 512,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );
           
        // Create the private and public key
        $res = openssl_pkey_new($config);
        
        // Extract the private key from $res to $privKey
        openssl_pkey_export($res, $privKey);
        
        // Extract the public key from $res to $pubKey
        $pubKey = openssl_pkey_get_details($res);
        $pubKey = $pubKey["key"];

        $pvkey = base64_encode($privKey);
        $pbkey = base64_encode($pubKey);

        $ret = array($pvkey, $pbkey);
        return $ret;
    } // end function 


    function gr_bytes(){
        $bytes = openssl_random_pseudo_bytes(4, $cstrong);
        $hex = bin2hex($bytes);
        return $hex;
    }


    function gen_user_hash($fname, $oname, $sname, $bmonth, $byear, $secret){
        //gen salt
        $salt = hash("sha512", $secret);

        //convert names to lower case
        $fname = strtolower($fname);
        $oname = strtolower($oname);
        $sname = strtolower($sname);
        $bmonth = strtolower($bmonth);
        $byear = strtolower($byear);

        $cost = 10;
        $param='$'.implode('$',array(
            "2y", //select the most secure version of blowfish (>=PHP 5.3.7)
            str_pad($cost,2,"0",STR_PAD_LEFT), //add the cost in two digits
            $salt //add the salt
        ));
        

        $combine = "$fname.$oname.$sname.$bmonth.$byear.$secret";
        $hash = hash("sha512", $combine);
        $phash = crypt($hash, $param);
        $fhash = hash("sha512", $phash); 
        return $fhash;
    } //end function
} //end class

?>