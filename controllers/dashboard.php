<?php
    class DashboardController extends ControllerBase {
        public function view() {
            if ( isset( $_SESSION[ 'user' ] ) ) {
                $username = $_SESSION[ 'user' ][ 'username' ];
            }
            include 'views/header.php';
            include 'views/home.php';
            include 'views/footer.php';
        }
    }
?>
