<?php
    class FileController {
        public static function listing() {
            if ( isset( $_SESSION[ 'userid' ] ) ) {
                $username = $_SESSION[ 'username' ];
            }
            include 'views/home.php';
        }
    }
?>
