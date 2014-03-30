<?php
    class SessionController extends ControllerBase {
        public function create( $username = '', $password = '', $persistent = '' ) {
            global $config;

            require_once 'models/user.php';
            if ( empty( $username ) ) {
                go( 'session', 'create', [ 'usernameEmpty' => true ] );
            }
            if ( empty( $password ) ) {
                go( 'session', 'create', [ 'passwordEmpty' => true ] );
            }
            try {
                $user = User::findByUsername( $username );
            }
            catch ( ModelNotFoundException $e ) {
                go( 'session', 'create', [ 'usernameWrong' => true ] );
            }
            if ( !$user->authenticatesWithPassword( $password ) ) {
                go( 'session', 'create', [ 'passwordWrong' => true ] );
            }
            if ( $persistent ) {
                $user->renewSessionId();
                setcookie(
                    $config[ 'persistentCookie' ][ 'name' ],
                    $user->sessionid,
                    time() + $config[ 'persistentCookie' ][ 'duration' ]
                );
            }
            $_SESSION[ 'user' ] = $user;
            go();
        }

        public function delete() {
            global $config;

            unset( $_SESSION[ 'user' ] );
            setcookie(
                $config[ 'persistentCookie' ][ 'name' ],
                '',
                time() - $config[ 'persistentCookie' ][ 'unsetTime' ]
            );
            go();
        }

        public function createView( $passwordWrong, $usernameEmpty, $passwordEmpty, $usernameWrong ) {
            require 'views/session/create.php';
        }
    }
?>
