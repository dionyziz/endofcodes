<?php
    class UserController {
        public static function create( $username = '', $password = '', $email = '' ) {
            include 'models/users.php';
            $_SESSION[ 'create_post' ][ 'username' ] = $username;
            $_SESSION[ 'create_post' ][ 'email' ] = $email;
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
                if ( User::exists( $username ) ) {
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
                die();
            }
        }

        public static function view( $username, $notvalid ) {
            if ( $username === NULL ) {
                header( 'Location: index.php?resource=dashboard&method=view' );
            }
            include 'models/users.php';
            $credentials = User::get( $username );
            if ( $credentials === NULL ) {
                die( 'There was an error on function view at controllers/user.php' );
            }
            include 'views/profile.php';
        }

        public static function update( $password1, $password2, $password3 ) {
            include 'models/users.php';
            $username = $_SESSION[ 'user' ][ 'username' ];
            if ( User::authenticateUser( $username, $password1 ) ) {
                if ( $password2 === $password3 ) {
                    if ( strlen( $password2 ) <= 6 ) {
                        header( 'Location: index.php?resource=user&method=update&small_pass=yes' );
                        die();
                    }
                    User::update( $username, $password2 );
                    header( 'Location: index.php?resource=dashboard&method=view' );
                }
                else {
                    header( 'Location: index.php?resource=user&method=update&not_matched=yes' );
                    die();
                }
            }
            else {
                header( 'Location: index.php?resource=user&method=update&old_pass=yes' );
                die();
            }
        }

        public static function delete() {
            include 'models/users.php';
            $username = $_SESSION[ 'user' ][ 'username' ];
            unset( $_SESSION[ 'user' ] );
            User::delete( $username );
            header( 'Location: index.php?resource=dashboard&method=view' );
        }

        public static function createView( $empty, $user_used, $small_pass, $mail_used, $mail_notvalid ) {
            include 'views/register.php';
        }

        public static function updateView( $small_pass, $not_matched, $old_pass ) {
            include 'views/passreset.php';
        }
    }
?>
