<?php
    class SessionController extends ControllerBase {
        public function create( $username = '', $password = '' ) {
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
            $_SESSION[ 'user' ] = $user;
            go();
        }

        public function delete() {
            unset( $_SESSION[ 'user' ] );
            go();
        }

        public function createView( $password_wrong, $username_empty, $password_empty, $username_wrong ) {
            include 'views/session/create.php';
        }
    }
?>
