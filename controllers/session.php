<?php
    class SessionController {
        public static function create( $username = '', $password = '' ) {
            include 'models/users.php';
            if ( !empty( $username ) && !empty( $password ) ) {
                if ( $id = User::authenticateUser( $username, $password ) ) {
                    $_SESSION[ 'user' ] = array(
                        'userid' => $id,
                        'username' => $username
                    );
                    throw new RedirectException( 'index.php?resource=dashboard&method=view' );
                }
                throw new RedirectException( 'index.php?resource=session&method=create&error=yes' );
            }
            if ( empty( $username ) ) {
                throw new RedirectException( 'index.php?empty_user=yes&resource=session&method=create' );
            }
            throw new RedirectException( 'index.php?empty_pass=yes&resource=session&method=create' );
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
