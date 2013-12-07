<?php
    class DashboardController {
        public static function view() {
            if ( isset( $_SESSION[ 'user' ] ) ) {
                $username = $_SESSION[ 'user' ][ 'username' ];
            }
            include_once 'views/header.php';
            include_once 'views/home.php';
            include_once 'views/footer.php';
        }
    }
?>
