<?php
    class ForgotPasswordRequestController extends ControllerBase {
        public function create( $username ) {
            if ( empty( $username ) ) {
                go( 'forgotpasswordrequest', 'create', array( 'username_empty' => true ) );
            }
            try {
                $user = User::findByUsername( $username );
            }
            catch ( ModelNotFoundException $e ) {
                go( 'forgotpasswordrequest', 'create', array( 'username_not_exists' => true ) );
            }
            $link = $user->createForgotPasswordLink();
            include 'views/user/forgot/link.php'; 
        }
        public function view( $token, $username ) {
            try {
                $user = User::findByUsername( $username );
            }
            catch ( ModelNotFoundException $e ) {
                throw new HTTPNotFoundException();
            }
            try {
                $user->revokePasswordCheck( $token ); 
                $_SESSION[ 'user' ] = $user;
                go( 'forgotpasswordrequest', 'update' );
            }
            catch ( ModelValidationException $e ) {
                go( 'forgotpasswordrequest', 'update', [ $e->error => true ] );
            }
        }
        public function update( $password, $password_repeat ) {
            if ( empty( $password ) ) {
                go( 'forgotpasswordrequest', 'update', array( 'password_empty' => true ) );
            }
            if ( strlen( $password ) < 6 ) {
                go( 'forgotpasswordrequest', 'update', array( 'password_invalid' => true ) );
            }
            if ( $password !== $password_repeat ) {
                go( 'forgotpasswordrequest', 'update', array( 'password_not_matched' => true ) );
            }
            $user = $_SESSION[ 'user' ];
            $user->password = $password;
            $user->forgotPasswordToken = $user->forgotPasswordRequestCreated = null;
            $user->save();
            go();
        } 
        public function createView( $created, $link, $username_empty, $username_not_valid, $username_not_exists ) {
            include 'views/user/passwordrevoke.php';
        }
        public function updateView( $link_expired, $password_empty, $password_invalid, $password_not_matched ) {
            if ( $link_expired ) {
                include 'views/user/forgot/expired.php';
            }
            else {
                include 'views/user/forgot/reset.php'; 
            }
        }
    }

?>
