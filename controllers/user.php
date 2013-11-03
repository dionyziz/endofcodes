<?php
    class UserController {
        public static function create() {
            include 'models/users.php';
            if ( !empty( $_POST[ 'username' ] ) && !empty( $_POST[ 'password' ] ) && !empty( $_POST[ 'email' ] ) ) {
                $username = $_POST[ 'username' ];
                $password = $_POST[ 'password' ];
                $email = $_POST[ 'email' ];
                $posat = strrpos( $email, "@" );
                $posdot = strrpos( $email, "." );
                if ( $posat < 1 || $posat === false || $posdot == strlen( $email ) || $posdot === false ) {
                    header( 'Location: views/register.php?mail_notvalid=yes' );
                    die();
                }
                if ( userExists( $username ) ) {
                    header( 'Location: views/register.php?user_used=yes' );
                    die();
                }
                else if ( mailExists( $email ) ) {
                    header( 'Location: views/register.php?mail_used=yes' );
                    die();
                }
                createUser( $username, $password, $email );
                $id = authenticateUser( $username, $password );
                $_SESSION[ 'userid' ] = $id; 
                $_SESSION[ 'username' ] = $username;
                header( 'Location: index.php?resource=dashboard&method=listing' );
            }
            else {
                header( 'Location: views/register.php?empty=yes' );
            }
        }
    }
?>
