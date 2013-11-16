<?php
    class UserController {
        public static function create( $username = '', $password = '', $email = '' ) {
            if ( empty( $username ) ) {
                go( 'user', 'create', array( 'empty_user' => true ) );
            }
            if ( empty( $password ) ) {
                go( 'user', 'create', array( 'empty_pass' => true ) );
            }
            if ( empty( $email ) ) {
                go( 'user', 'create', array( 'empty_mail' => true ) );
            }
            include 'models/users.php';
            include 'models/mail.php';
            $_SESSION[ 'create_post' ] = array(
                'username' => $username,
                'email' => $email
            );
            try {
                $id = User::create( $username, $password, $email );
            }
            catch( ModelValidationException $e ) {
                go( 'user', 'create', array( $e->error => true ) );
            }
            $_SESSION[ 'user' ] = array(
                'userid' => $id,
                'username' => $username
            );
            go();
        }

        public static function view( $username, $notvalid ) {
            if ( $username === NULL ) {
                throw new HTTPNotFoundException();
            }
            include 'models/users.php';
            include 'models/extentions.php';
            include 'models/image.php';
            $credentials = User::get( $username );
            $config = getConfig();
            if ( !$credentials ) {
                throw new HTTPNotFoundException();
            }
            $avatarname = Image::getCurrentImage( $username );
            $target_path = $config[ 'paths' ][ 'avatar_path' ] . $avatarname;
            include 'views/user/view.php';
        }

        public static function update( $password_old, $password_new, $password_repeat ) {
            include 'models/users.php';
            if ( !isset( $_SESSION[ 'user' ] ) ) {
                throw new HTTPUnauthorizedException();
            }
            $username = $_SESSION[ 'user' ][ 'username' ];
            if ( User::authenticate( $username, $password_old ) ) {
                if ( $password_new != $password_repeat ) {
                    go( 'user', 'update', array( 'not_matched' => true ) );
                }
                User::update( $username, $password_new );
                go();
            }
            go( 'user', 'update', array( 'old_pass' => true ) );
        }

        public static function delete() {
            include 'models/users.php';
            if ( !isset( $_SESSION[ 'user' ] ) ) {
                throw new HTTPUnauthorizedException();
            }
            $username = $_SESSION[ 'user' ][ 'username' ];
            unset( $_SESSION[ 'user' ] );
            User::delete( $username );
            go();
        }

        public static function createView( $empty_user, $empty_mail, $empty_pass, $user_used, $small_pass, $mail_used, $mail_notvalid ) {
            include 'views/user/create.php';
        }

        public static function updateView( $small_pass, $not_matched, $old_pass ) {
            include 'views/user/update.php';
        }
    }
?>
