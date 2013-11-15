<?php
    class Mail {
        public function validMail( $mail ) {
            $posat = strrpos( $mail, "@" );
            $posdot = strrpos( $mail, "." );
            if ( $posat < 1 || $posat === false || $posdot === strlen( $mail ) || $posdot === false ) {
                return false;
            }
            return true;
        }
    }
?>
