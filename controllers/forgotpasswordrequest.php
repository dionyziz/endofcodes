<?php
    class ForgotPasswordRequestController extends ControllerBase {
        public function create( $input ) {
            if ( empty( $input ) ) {
                go( 'forgotpasswordrequest', 'create', [ 'input_empty' => true ] );
            }
            if ( filter_var( $input, FILTER_VALIDATE_EMAIL ) ) {
                try {
                    $user = User::findByEmail( $input );
                }    
                catch ( ModelNotFoundException $e ) {
                    go( 'forgotpasswordrequest', 'create', [ 'email_not_exists' => true ] );
                }
            } else {
                try {
                    $user = User::findByUsername( $input );
                }
                catch ( ModelNotFoundException $e ) {
                    go( 'forgotpasswordrequest', 'create', [ 'username_not_exists' => true ] );
                }
            }
            $link = $user->createForgotPasswordLink();
            include 'views/user/forgot/link.php'; 
        }
        public function view( $passwordToken, $username ) {
            try {
                $user = User::findByUsername( $username );
            }
            catch ( ModelNotFoundException $e ) {
                throw new HTTPNotFoundException();
            }
            try {
                $user->revokePasswordCheck( $passwordToken ); 
                $_SESSION[ 'user' ] = $user;
                go( 'forgotpasswordrequest', 'update', [ 'passwordToken' => $passwordToken ] );
            }
            catch ( ModelValidationException $e ) {
                go( 'forgotpasswordrequest', 'update', [ $e->error => true ] );
            }
        }
        public function update( $password, $password_repeat, $passwordToken ) {
            if ( $password !== $password_repeat ) {
                go( 'forgotpasswordrequest', 'update', [ 'password_not_matched' => true ] );
            }
            if ( isset( $_SESSION[ 'user' ] ) ) {
                $user = $_SESSION[ 'user' ];
            }
            else {
                throw new HTTPUnauthorizedException();
            }
            try {
                $user->revokePasswordCheck( $passwordToken );
            }
            catch ( HTTPUnauthorizedException $e ) {
                throw $e;
            }
            try {
                $user::passwordValidate( $password );
            }
            catch ( ModelValidationException $e ) {
                go( 'forgotpasswordrequest', 'update', [ $e->error => true ] );
            }
            $user->password = $password;
            $user->forgotPasswordToken = $user->forgotPasswordRequestCreated = null;
            $user->save();
            go();
        } 
        public function createView( $input_empty, $username_not_exists, $email_not_exists ) {
            include 'views/user/passwordrevoke.php';
        }
        public function updateView( $link_expired, $password_empty, $password_invalid, $password_not_matched, $passwordToken ) {
            if ( $link_expired ) {
                include 'views/user/forgot/expired.php';
            }
            else {
                include 'views/user/forgot/reset.php'; 
            }
        }
    }
?>
