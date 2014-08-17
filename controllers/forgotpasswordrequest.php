<?php
    class ForgotPasswordRequestController extends AuthenticatedController {
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
            $user->createForgotPasswordLink();
            flash( 'You will shortly receive an e-mail with instructions to reset your password.' );
            go();
        }
        public function update( $password, $password_repeat, $password_token ) {
            if ( $password !== $password_repeat ) {
                go( 'forgotpasswordrequest', 'update', [ 'password_not_matched' => true, 'password_token' => $password_token ] );
            }
            $this->requireLogin();
            $user = $_SESSION[ 'user' ];
            try {
                $user->revokePasswordCheck( $password_token );
            }
            catch ( ForgotPasswordModelInvalidTokenException $e ) {
                throw new HTTPUnauthorizedException( 'Password token specified is invalid' );
            }
            try {
                $user::passwordValidate( $password );
            }
            catch ( ModelValidationException $e ) {
                go( 'forgotpasswordrequest', 'update', [ $e->error => true, 'password_token' => $password_token ] );
            }
            $user->password = $password;
            $user->forgotpasswordtoken = $user->forgotpasswordrequestCreated = null;
            $user->save();
            go();
        } 
        public function createView( $input_empty, $username_not_exists, $email_not_exists ) {
            include 'views/user/passwordrevoke.php';
        }
        public function updateView( $username, $password_empty, $password_invalid, $password_not_matched, $password_token ) {            
            if ( !empty( $password_empty ) && !empty( $password_not_matched ) && !empty( $password_invalid ) ) {
                include 'views/user/forgot/reset.php'; 
                return;
            }
            if ( !empty( $username ) ) {
                try {
                    $user = User::findByUsername( $username );
                }
                catch ( ModelNotFoundException $e ) {
                    throw new HTTPNotFoundException( 'No such username "' . $username . '"' );
                }
                try {
                    $user->revokePasswordCheck( $password_token );
                    $_SESSION[ 'user' ] = $user;
                }
                catch ( ForgotPasswordModelInvalidTokenException $e ) {
                    throw new HTTPUnauthorizedException( 'Password token specified is invalid' );
                }
            }
            if ( empty( $_SESSION[ 'user' ] ) ) {
                throw new HTTPUnauthorizedException( 'You have not specified any user' );
            }
            $user = $_SESSION[ 'user' ];
            try {
                $user->revokePasswordCheck( $password_token ); 
            }
            catch ( ModelValidationException $e ) {
                if ( $e->error == 'link_expired' )  {
                    include 'views/user/forgot/expired.php';
                }
            }
            include 'views/user/forgot/reset.php'; 
        }
    }
?>
