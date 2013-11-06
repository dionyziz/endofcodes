<?php
    class SessionController {
        public static function create( $username = '', $password = '' ) {
            include 'models/users.php';
            if ( !empty( $username ) && !empty( $password ) ) {
                if ( $id = User::authenticateUser( $username, $password ) ) {
                    $_SESSION[ 'userid' ] = $id;
                    $_SESSION[ 'username' ] = $username;
                    header( 'Location: index.php?resource=dashboard&method=listing' );
                }
                else {
                    header( 'Location: index.php?resource=session&method=create&error=yes' );
                }
            }
            else {
                header( 'Location: index.php?empty=yes&resource=session&method=create' );
            }
        }

        public static function delete() {
            unset( $_SESSION[ 'userid' ] );
            header( 'Location: index.php?resource=dashboard&method=listing' );
        }

        public static function createView( $error, $empty ) {
            include 'views/login.php';
        }

        public static function deleteView() {
            SessionController::delete();
        }
    }
?>
