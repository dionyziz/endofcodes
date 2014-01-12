<?php
    Class FormToken {
        public static function create() {
            $token = openssl_random_pseudo_bytes( 32 );
            return base64_encode( $token );
        }
    }
?>

