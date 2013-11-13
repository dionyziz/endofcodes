<?php
    class UserController {
        public static function create( $username = '', $password = '', $email = '' ) {
            include 'models/users.php';
            $_SESSION[ 'create_post' ][ 'username' ] = $username;
            $_SESSION[ 'create_post' ][ 'email' ] = $email;
            if ( !empty( $username ) && !empty( $password ) && !empty( $email ) ) {
                if ( User::exists( $username ) ) {
                    throw new RedirectException( 'index.php?user_used=yes&resource=user&method=create' );
                }
                else if ( User::mailExists( $email ) ) {
                    throw new RedirectException( 'index.php?mail_used=yes&resource=user&method=create' );
                }
                User::createUser( $username, $password, $email );
                $id = User::authenticateUser( $username, $password );
                $_SESSION[ 'user' ] = array(
                    'userid' => $id,
                    'username' => $username
                );
                throw new RedirectException( 'index.php?resource=dashboard&method=view' );
            }
            else {
                throw new RedirectException( 'index.php?empty=yes&resource=user&method=create' );
            }
        }

        public static function view( $username, $notvalid ) {
            if ( $username === NULL ) {
                throw new RedirectException( 'index.php?resource=dashboard&method=view' );
            }
            include 'models/users.php';
            include 'models/extentions.php';
            include 'models/image.php';
            include 'config/paths.php';
            $credentials = User::get( $username );
            if ( !$credentials ) {
                throw new Exception( 'can\'t get credentials' );
            }
            $avatarname = Image::getCurrentImage( $username );
            $target_path = getUploadPath() . $avatarname;
            include 'views/profile.php';
        }

        public static function update( $password_old, $password_new, $password_repeat ) {
            include 'models/users.php';
            $username = $_SESSION[ 'user' ][ 'username' ];
            if ( User::authenticateUser( $username, $password_old ) ) {
                if ( $password_new != $password_repeat ) {
                    throw new RedirectException( 'index.php?resource=user&method=update&not_matched=yes' );
                }
                else {
                    if ( strlen( $password_new ) <= 6 ) {
                        throw new RedirectException( 'index.php?resource=user&method=update&small_pass=yes' );
                    }
                    User::update( $username, $password_new );
                    throw new RedirectException( 'index.php?resource=dashboard&method=view');
                }
            }
            else {
                throw new RedirectException( 'index.php?resource=user&method=update&old_pass=yes' );
            }
        }

        public static function delete() {
            include 'models/users.php';
            $username = $_SESSION[ 'user' ][ 'username' ];
            unset( $_SESSION[ 'user' ] );
            User::delete( $username );
            throw new RedirectException( 'index.php?resource=dashboard&method=view' );
        }

        public static function createView( $empty, $user_used, $small_pass, $mail_used, $mail_notvalid ) {
            include 'views/register.php';
        }

        public static function updateView( $small_pass, $not_matched, $old_pass ) {
            include 'views/passreset.php';
        }
    }
?>
