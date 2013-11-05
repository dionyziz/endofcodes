<?php
    class DashboardController {
        public static function listing() {
            if ( isset( $_SESSION[ 'userid' ] ) ) {
                $username = $_SESSION[ 'username' ];
            }
            else if ( isset( $_GET[ 'wrong_contr' ] ) ) {
            }
            view( "home", $username );
        }
    }
?>
