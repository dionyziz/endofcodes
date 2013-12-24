<?php
    Class FormToken {
        public static function validate( $token ) {
            if ( $_SESSION[ 'form' ][ 'token' ] == $token ) {
                return true;
            } 
        }
        
        public static function create( $num = 20 ) {
            $token = md5( uniqid() );
            $_SESSION[ 'form' ][ 'token'] = $token;  
            return $token;
        }
    }
?>

