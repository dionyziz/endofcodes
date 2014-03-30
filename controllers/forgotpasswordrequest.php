<?php
    class ForgotPasswordRequestController extends ControllerBase {
        public function create( $input ) {
            if ( empty( $input ) ) {
                go( 'forgotpasswordrequest', 'create', [ 'inputEmpty' => true ] );
            }
            if ( filter_var( $input, FILTER_VALIDATE_EMAIL ) ) {
                try {
                    $user = User::findByEmail( $input );
                }    
                catch ( ModelNotFoundException $e ) {
                    go( 'forgotpasswordrequest', 'create', [ 'emailNotExists' => true ] );
                }
            } 
            else {
                try {
                    $user = User::findByUsername( $input );
                }
                catch ( ModelNotFoundException $e ) {
                    go( 'forgotpasswordrequest', 'create', [ 'usernameNotExists' => true ] );
                }
            }
            $user->createForgotPasswordLink();
            include 'views/user/forgot/link.php'; 
        }
        public function update( $password, $passwordRepeat, $passwordToken ) {
            if ( $password !== $passwordRepeat ) {
                go( 'forgotpasswordrequest', 'update', [ 'passwordNotMatched' => true, 'passwordToken' => $passwordToken ] );
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
            catch ( ForgotPasswordModelInvalidTokenException $e ) {
                throw new HTTPUnauthorizedException();
            }
            try {
                $user::passwordValidate( $password );
            }
            catch ( ModelValidationException $e ) {
                go( 'forgotpasswordrequest', 'update', [ $e->error => true, 'passwordToken' => $passwordToken ] );
            }
            $user->password = $password;
            $user->forgotpasswordtoken = $user->forgotpasswordrequestCreated = null;
            $user->save();
            go();
        } 
        public function createView( $inputEmpty, $usernameNotExists, $emailNotExists ) {
            include 'views/user/passwordrevoke.php';
        }
        public function updateView( $username, $passwordEmpty, $passwordInvalid, $passwordNotMatched, $passwordToken ) {            
            if ( !empty( $passwordEmpty ) && !empty( $passwordNotMatched ) && !empty( $passwordInvalid ) ) {
                include 'views/user/forgot/reset.php'; 
                return;
            }
            if ( !empty( $username ) ) {
                try {
                    $user = User::findByUsername( $username );
                }
                catch ( ModelNotFoundException $e ) {
                    throw new HTTPNotFoundException();
                }
                try {
                    $user->revokePasswordCheck( $passwordToken );
                    $_SESSION[ 'user' ] = $user;
                }
                catch ( ForgotPasswordModelInvalidTokenException $e ) {
                    throw new HTTPUnauthorizedException();
                }
            }
            if ( !empty( $_SESSION[ 'user' ] ) ) {
                $user = $_SESSION[ 'user' ];
            }
            try {
                $user->revokePasswordCheck( $passwordToken ); 
            }
            catch ( ModelValidationException $e ) {
                if ( $e->error == 'linkExpired' )  {
                    include 'views/user/forgot/expired.php';
                }
            }
            include 'views/user/forgot/reset.php'; 
        }
    }
?>
