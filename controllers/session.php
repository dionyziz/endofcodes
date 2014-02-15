<?php
    class SessionController extends ControllerBase {
        public function create( $username = '', $password = '', $persistent = '' ) {
            global $config;

            require_once 'models/user.php';
            if ( empty( $username ) ) {
                go( 'session', 'create', [ 'username_empty' => true ] );
            }
            if ( empty( $password ) ) {
                go( 'session', 'create', [ 'password_empty' => true ] );
            }
            try {
                $user = User::findByUsername( $username );
            }
            catch ( ModelNotFoundException $e ) {
                go( 'session', 'create', [ 'username_wrong' => true ] );
            }
            if ( !$user->authenticatesWithPassword( $password ) ) {
                go( 'session', 'create', [ 'password_wrong' => true ] );
            }
            if ( $persistent ) {
                $user->renewSessionId();
                setcookie(
                    $config[ 'persistent_cookie' ][ 'name' ],
                    $user->sessionid,
                    time() + $config[ 'persistent_cookie' ][ 'duration' ]
                );
            }
            $_SESSION[ 'user' ] = $user;
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
            require 'views/session/create.php';
        }
    }
?>
