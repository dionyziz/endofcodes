<?php
    class SessionController {
        public static function create( $username = '', $password = '' ) {
            include_once 'models/user.php';
            if ( empty( $username ) ) {
                go( 'session', 'create', array( 'username_empty' => true ) );
            }
            if ( empty( $password ) ) {
                go( 'session', 'create', array( 'password_empty' => true ) );
            }
            try {
                $user = User::findByUsername( $username );
            }
            catch ( ModelNotFoundException $e ) {
                go( 'session', 'create', array( 'username_wrong' => true ) );
            }
            if ( !$user->authenticatesWithPassword( $password ) ) {
                go( 'session', 'create', array( 'password_wrong' => true ) );
            }
            $id = $user->id;
            $_SESSION[ 'user' ] = compact( 'id', 'username' );
            go();
        }

        public static function delete() {
            unset( $_SESSION[ 'user' ] );
            go();
        }

        public static function createView( $password_wrong, $username_empty, $password_empty, $username_wrong ) {
            include 'views/session/create.php';
        }
    }
?>
