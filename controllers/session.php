<?php
    class SessionController {
        public static function create() {
            include 'models/users.php';
            $password = $_POST[ 'password' ];
            $username = $_POST[ 'username' ];
            if ( isset( $username ) && isset( $password ) ) {
                if ( $id = athenticateUser( $username, $password ) ) {
                    $_SESSION[ 'userid' ] = $id;
                    $_SESSION[ 'username' ] = $username;
                    header( 'Location: index.php?resource=file&method=listing' );
                }
                else {
                    header( 'Location: views/login.php?error=yes' );
                }
            }
            else {
                header( 'Location: views/login.php?empty=yes' );
            }
        }

        public static function delete() {
            unset( $_SESSION[ 'userid' ] );
            header( 'Location: index.php?resource=file&method=listing' );
        }
    }
?>
