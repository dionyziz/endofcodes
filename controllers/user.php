<?php
    class UserController {
        public static function create() {
            include 'models/users.php';
            include 'views/specialchars.php';
            $username = specialChars( $_POST[ 'username' ] );
            $password = specialChars( $_POST[ 'password' ] );
            $email = specialChars( $_POST[ 'email' ] );
            if ( isset( $username ) && isset( $password ) && isset( $email ) ) {
                if ( userExists( $username ) ) {
                    header( 'Location: views/register.php?user_used=yes' );
                    die();
                }
                else if ( mailExists( $email ) ) {
                    header( 'Location: views/register.php?mail_used=yes' );
                    die();
                }
                createUser( $username, $password, $email );
                $id = athenticateUser( $username, $password );
                $_SESSION[ 'userid' ] = $id; 
                $_SESSION[ 'username' ] = $username;
                header( 'Location: index.php?resource=file&method=listing' );
            }
            else {
                header( 'Location: views/register.php?empty=yes' );
            }
        }
    }
?>
