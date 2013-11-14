<?php
    class Mail {
        public function mailExists( $mail ) {
            $res = db(
                'SELECT
                    username
                FROM
                    users
                WHERE
                    email = :mail
                LIMIT 1;', 
                compact( "mail" ) 
            );
            if ( mysql_num_rows( $res ) == 1 ) {
                return true;
            }
            return false;
        }

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
