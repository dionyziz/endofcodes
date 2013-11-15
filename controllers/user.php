<?php
    class UserController {
        public static function create( $username = '', $password = '', $email = '' ) {
            include 'models/users.php';
            include 'models/mail.php';
            $_SESSION[ 'create_post' ] = array(
                'username' => $username,
                'email' => $email
            );
            if ( !empty( $username ) && !empty( $password ) && !empty( $email ) ) {
                if ( User::exists( $username ) ) {
                    throw new RedirectException( 'index.php?user_used=yes&resource=user&method=create' );
                }
                try {
                    $id = User::create( $username, $password, $email );
                }
                catch( ModelValidationException $e ) {
                    throw new RedirectException( 'index.php?resource=user&method=create&' . $e->error . '=yes' );
                }
                $_SESSION[ 'user' ] = array(
                    'userid' => $id,
                    'username' => $username
                );
                throw new RedirectException( 'index.php?resource=dashboard&method=view' );
            }
            if ( empty( $username ) ) {
                throw new RedirectException( 'index.php?empty_user=yes&resource=user&method=create' );
            }
            if ( empty( $password ) ) {
                throw new RedirectException( 'index.php?empty_pass=yes&resource=user&method=create' );
            }
            throw new RedirectException( 'index.php?empty_mail=yes&resource=user&method=create' );
        }

        public static function view( $username, $notvalid ) {
            if ( $username === NULL ) {
                throw new RedirectException( 'index.php?resource=dashboard&method=view' );
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
            if ( User::authenticateUser( $username, $password_old ) ) {
                if ( $password_new != $password_repeat ) {
                    throw new RedirectException( 'index.php?resource=user&method=update&not_matched=yes' );
                }
                User::update( $username, $password_new );
                throw new RedirectException( 'index.php?resource=dashboard&method=view');
            }
            throw new RedirectException( 'index.php?resource=user&method=update&old_pass=yes' );
        }

        public static function delete() {
            include 'models/users.php';
            if ( !isset( $_SESSION[ 'user' ] ) ) {
                throw new HTTPUnauthorizedException();
            }
            $username = $_SESSION[ 'user' ][ 'username' ];
            unset( $_SESSION[ 'user' ] );
            User::delete( $username );
            throw new RedirectException( 'index.php?resource=dashboard&method=view' );
        }

        public static function createView( $empty_user, $empty_mail, $empty_pass, $user_used, $small_pass, $mail_used, $mail_notvalid ) {
            include 'views/user/create.php';
        }

        public static function updateView( $small_pass, $not_matched, $old_pass ) {
            include 'views/user/update.php';
        }
    }
?>
