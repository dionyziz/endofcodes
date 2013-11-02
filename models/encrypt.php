<?php
    function encrypt( $password ) {
        $salt = openssl_random_pseudo_bytes( 32 );
        $hash = hash( 'sha256', $password . $salt );
        return array( "password" => "$hash", "salt" => "$salt" );
    }
?>
