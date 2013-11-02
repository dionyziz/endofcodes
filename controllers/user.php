<?php
    class UserController {
        public static function create() {
            include 'models/users.php';
            if ( isset( $_POST[ 'username' ] ) && isset( $_POST[ 'password' ] ) && isset( $_POST[ 'email' ] ) ) {
                $username = htmlspecialchars( $_POST[ 'username' ] );
                $password = htmlspecialchars( $_POST[ 'password' ] );
                $email = htmlspecialchars( $_POST[ 'email' ] );
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
