<?php
    class SessionController {
        public static function create( $username = '', $password = '' ) {
            include 'models/users.php';
            if ( empty( $username ) ) {
                throw new RedirectException( 'index.php?empty_user=yes&resource=session&method=create' );
            }
            if ( empty( $password ) ) {
                throw new RedirectException( 'index.php?empty_pass=yes&resource=session&method=create' );
            }
            $id = User::authenticateUser( $username, $password );
            if ( $id == false ) {
                throw new RedirectException( 'index.php?resource=session&method=create&error=yes' );
            }
            $_SESSION[ 'user' ] = array(
                'userid' => $id,
                'username' => $username
            );
            throw new RedirectException( 'index.php?resource=dashboard&method=view' );
        }

        public static function delete() {
            unset( $_SESSION[ 'user' ] );
            throw new RedirectException( 'index.php?resource=dashboard&method=view' );
        }

        public static function createView( $error, $empty_user, $empty_pass ) {
            include 'views/session/create.php';
        }
    }
?>
