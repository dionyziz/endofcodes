<?php

    class ForgotPasswordRequestController extends ControllerBase {
        public function create( $username ) {
           if ( empty( $username ) ) {
                go( 'forgotPasswordRequest', 'create', array( 'username_empty' => true ) );
            }
            try {
                $user = User::findByUsername( $username );
            }
            catch ( ModelNotFoundException $e ) {
                go( 'forgotPasswordRequest', 'create', array( 'username_not_exists' => true ) );
            }
            try {
                $link = $user->createForgotPasswordLink();
            }
            catch ( ModelNotFoundException $e  ) {
                echo 'bad';
            }
            include 'views/forgotPasswordLink.php'; 
            
        }
        public function view( $token ='', $username= '' ) {
            $token = $_GET[ 'token' ];
            $username = $_GET[ 'username' ];
            try {
                $user = User::findByUsername( $username );
            }
            catch ( ModelNotFoundException $e ) {
            }
            $user->revokePassword( $token ); 
        }
        public function update() {
        } 
        public function delete() {
        }
        public function createView( $username_empty, $username_not_valid, $username_not_exists ) {
            include 'views/pass_revoke.php';
        }
        public function updateView() {
        }
    
    }

?>
