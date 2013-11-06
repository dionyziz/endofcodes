<?php
    class DashboardController {
        public static function listing() {
            if ( isset( $_SESSION[ 'userid' ] ) ) {
                $username = $_SESSION[ 'username' ];
            }
            include 'views/header.php';
            include 'views/home.php';
            include 'views/footer.php';
        }
    }
?>
