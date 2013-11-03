<?php
    class DashboardController {
        public static function listing() {
            if ( isset( $_SESSION[ 'userid' ] ) ) {
                $username = $_SESSION[ 'username' ];
            }
            view( "home" );
        }
    }
?>
