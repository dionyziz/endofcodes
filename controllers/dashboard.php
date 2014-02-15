<?php
    class DashboardController extends ControllerBase {
        public function view() {
            if ( isset( $_SESSION[ 'user' ] ) ) {
                $user = $_SESSION[ 'user' ];
            }
            require 'views/header.php';
            require 'views/home.php';
            require 'views/footer.php';
        }
    }
?>
