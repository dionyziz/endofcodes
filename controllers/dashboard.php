<?php
    class DashboardController extends ControllerBase {
        public function view() {
            if ( isset( $_SESSION[ 'user' ] ) ) {
                $username = $_SESSION[ 'user' ][ 'username' ];
            }
            include_once 'models/formtoken.php';
            $token = FormToken::create();
            $_SESSION[ 'form' ][ 'token' ] = $token;  
            include 'views/header.php';
            include 'views/home.php';
            include 'views/footer.php';
        }
    }
?>
