<?php
    class SessionController extends ControllerBase {
        public function create( $username = '', $password = '', $persistent = '' ) {
            global $config;
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
            if ( $persistent == true ) {
                $sessionid = $user->createPersistentCookie();     
                setcookie(  
                    $config[ 'persistent_cookie'][ 'name' ], 
                    $sessionid, 
                    time() + $config[ 'persistent_cookie' ][ 'duration' ] 
                );
            }
            $id = $user->id;
            $_SESSION[ 'user' ] = compact( 'id', 'username' );
            go();
        }

        public function delete() {
            global $config; 
            unset( $_SESSION[ 'user' ] );
            setcookie( 
                $config[ 'persistent_cookie' ][ 'name' ], 
                '', 
                time() - $config[ 'persistent_cookie' ][ 'unset_time' ] 
            );
            go();
        }

        public function createView( $password_wrong, $username_empty, $password_empty, $username_wrong ) {
            include 'views/session/create.php';
        }
    }
?>
