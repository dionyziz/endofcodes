<?php
    Class FormToken {
        public static function validate( $_POST[ 'token' ] ) {
            if( $_SESSION[ 'form' ][ 'token' ] == $_POST[ 'token' ] ) {
                die ( 'form is good' );
            } 
            else {
                die( "fuck" );
            }
        }
        
        public static function create{
            return openssl_random_pseudo_bytes( 10 );
        }
    }
?>

