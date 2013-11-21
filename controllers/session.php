<?php
    class SessionController {
        public static function create( $username = '', $password = '' ) {
            include 'models/users.php';
            if ( empty( $username ) ) {
                go( 'session', 'create', array( 'empty_user' => true ) );
            }
            if ( empty( $password ) ) {
                go( 'session', 'create', array( 'empty_pass' => true ) );
            }
            $user = new User( $username, $password );
            $id = $user->authenticate();
            if ( $id == false ) {
                go( 'session', 'create', array( 'error' => true ) );
            }
            $_SESSION[ 'user' ] = array(
                'userid' => $id,
                'username' => $username
            );
            go();
        }

        public static function delete() {
            unset( $_SESSION[ 'user' ] );
            go();
        }

        public static function createView( $error, $empty_user, $empty_pass ) {
            include 'views/session/create.php';
        }
    }
?>
