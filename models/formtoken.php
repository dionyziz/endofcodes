<?php
    Class FormToken {
        public static function validate( $token_check, $token_valid ) {
            if ( $token_valid === $token_check ) {
                return true;
            } 
        }
        
        public static function create() {
            $token = openssl_random_pseudo_bytes( 32 );
            return base64_encode( $token );
        }
    }
?>

