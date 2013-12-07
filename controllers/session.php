<?php
    class SessionController {
        public static function create( $username = '', $password = '' ) {
            include_once 'models/user.php';
            if ( empty( $username ) ) {
                go( 'session', 'create', array( 'empty_user' => true ) );
            }
            if ( empty( $password ) ) {
                go( 'session', 'create', array( 'empty_pass' => true ) );
            }
            try {
                $user = User::find_by_username( $username );
            }
            catch ( ModelNotFoundException $e ) {
                go( 'session', 'create', array( 'wrong_user' => true ) );
            }
            if ( !$user->authenticatesWithPassword( $password ) ) {
                go( 'session', 'create', array( 'wrong_pass' => true ) );
            }
            $id = $user->id;
            $_SESSION[ 'user' ] = array(
                'id' => $id,
                'username' => $username
            );
            go();
        }

        public static function delete() {
            unset( $_SESSION[ 'user' ] );
            go();
        }

        public static function createView( $wrong_pass, $empty_user, $empty_pass, $wrong_user ) {
            include_once 'views/session/create.php';
        }
    }
?>
