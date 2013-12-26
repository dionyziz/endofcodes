<?php
    Class FormToken {
        public static function validate( $token_check, $token_valid ) {
            if ( $token_valid === $token_check ) {
                return true;
            } 
        }
        
        public static function create() {
            return md5( uniqid() );
        }
    }
?>

