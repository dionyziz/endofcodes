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
            } 
            else {
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
        public function update( $password, $password_repeat, $password_token ) {
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
                $user->revokePasswordCheck( $password_token );
            }
            catch ( HTTPUnauthorizedException $e ) {
                throw $e;
            }
            try {
                $user::passwordValidate( $password );
            }
            catch ( ModelValidationException $e ) {
                go( 'forgotpasswordrequest', 'update', [ $e->error => true, 'password_token' => $password_token ] );
            }
            $user->password = $password;
            $user->forgotPasswordToken = $user->forgotPasswordRequestCreated = null;
            $user->save();
            go();
        } 
        public function createView( $input_empty, $username_not_exists, $email_not_exists ) {
            include 'views/user/passwordrevoke.php';
        }
        public function updateView( $username, $link_expired, $password_empty, $password_invalid, 
                $password_not_matched, $password_token ) {            
            if ( isset( $password_empty ) || isset( $password_not_matched ) || isset( $password_invalid ) ) {
                include 'views/user/forgot/reset.php'; 
                return;
            }
            if ( isset( $username ) ) {
                try {
                    $user = User::findByUsername( $username );
                    $_SESSION[ 'user' ] = $user;
                }
                catch ( ModelNotFoundException $e ) {
                    throw new HTTPNotFoundException();
                }
            }
            else {
                $user = $_SESSION[ 'user' ];
            }
            try {
                $user->revokePasswordCheck( $password_token ); 
            }
            catch ( ModelValidationException $e ) {
                if ( $e->error = 'link_expired' )  {
                    include 'views/user/forgot/expired.php';
                }
                else {
                    throw $e;
                }
            }
            include 'views/user/forgot/reset.php'; 
        }
    }
?>
