<?php
    class SessionController extends ControllerBase {
        public function create( $username = '', $password = '', $token = '' ) {
            include_once 'models/formtoken.php';
            include_once 'models/user.php';
            if ( empty( $username ) ) {
                go( 'session', 'create', array( 'username_empty' => true ) );
            }
            if ( empty( $password ) ) {
                go( 'session', 'create', array( 'password_empty' => true ) );
            }
            if ( !FormToken::validate( $token, $_SESSION[ 'form' ][ 'token' ] ) ) {
                 throw new HTTPUnauthorizedException();
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

        public function delete( $token = '' ) {
            include_once 'models/formtoken.php';
            if ( !FormToken::validate( $token, $_SESSION[ 'form' ][ 'token' ] ) ) {
                 throw new HTTPUnauthorizedException();
            }
            unset( $_SESSION[ 'user' ] );
            go();
        }

        public function createView( $password_wrong, $username_empty, $password_empty, $username_wrong ) {
            include_once 'models/formtoken.php';
            $token = FormToken::create();
            $_SESSION[ 'form' ][ 'token' ] = $token;  
            include 'views/session/create.php';
        }
    }
?>
