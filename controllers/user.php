<?php
    class UserController {
        public static function create( $username = '', $password = '', $email = '' ) {
            include 'models/users.php';
            if ( !empty( $username ) && !empty( $password ) && !empty( $email ) ) {
                if ( strlen( $password ) <= 6 ) {
                    header( 'Location: index.php?resource=user&method=create&small_pass=yes' );
                    die();
                }
                $valid = User::validMail( $email );
                if ( !$valid ) {
                    header( 'Location: index.php?mail_notvalid=yes&resource=user&method=create' );
                    die();
                }
                if ( User::Exists( $username ) ) {
                    header( 'Location: index.php?user_used=yes&resource=user&method=create' );
                    die();
                }
                else if ( User::mailExists( $email ) ) {
                    header( 'Location: index.php?mail_used=yes&resource=user&method=create' );
                    die();
                }
                User::createUser( $username, $password, $email );
                $id = User::authenticateUser( $username, $password );
                $_SESSION[ 'user' ][ 'userid' ] = $id; 
                $_SESSION[ 'user' ][ 'username' ] = $username;
                header( 'Location: index.php?resource=dashboard&method=view' );
            }
            else {
                header( 'Location: index.php?empty=yes&resource=user&method=create' );
            }
        }

        public static function view( $username ) {
            if ( $username === NULL ) {
                header( 'Location: index.php?resource=dashboard&method=view' );
            }
            include 'models/users.php';
            $credentials = User::getCredentials( $username );
            if ( $credentials === NULL ) {
                die( 'There was an error on function view at controllers/user.php' );
            }
            include 'views/profile.php';
        }

        public static function update( $password ) {
            include 'models/users.php';
            $username = $_SESSION[ 'user' ][ 'username' ];
            User::updatePassword( $username, $password );
            header( 'Location: index.php?resource=dashboard&method=view' );
        }

        public static function delete() {
            include 'models/users.php';
            $username = $_SESSION[ 'user' ][ 'username' ];
            unset( $_SESSION[ 'user' ] );
            User::deleteUser( $username );
            header( 'Location: index.php?resource=dashboard&method=view' );
        }

        public static function createView( $empty, $user_used, $small_pass, $mail_used, $mail_notvalid ) {
            include 'views/register.php';
        }

        public static function updateView() {
            include 'views/passreset.php';
        }
    }
?>
