<?php
    class UserController {
        public static function create( $username = '', $password = '', $password_repeat = '', $email = '', $country, $accept = false ) {
            if ( $accept === false ) {
                go( 'user', 'create', array( 'not_accepted' => true ) );
            }
            if ( empty( $username ) ) {
                go( 'user', 'create', array( 'empty_user' => true ) );
            }
            if ( empty( $password ) ) {
                go( 'user', 'create', array( 'empty_pass' => true ) );
            }
            if ( empty( $email ) ) {
                go( 'user', 'create', array( 'empty_mail' => true ) );
            }
            if ( empty( $password_repeat ) ) {
                go( 'user', 'create', array( 'empty_pass_repeat' => true ) );
            }
            if ( $country === 'Select Country' ) {
                go( 'user', 'create', array( 'empty_country' => true ) );
            }
            if ( $password !== $password_repeat ) {
                go( 'user', 'create', array( 'not_matched' => true ) );
            }
            include 'models/user.php';
            include 'models/country.php';
            $_SESSION[ 'create_post' ] = array(
                'username' => $username,
                'email' => $email
            );
            $user = new User();
            $user->username = $username;
            $user->password = $password;
            $user->email = $email;
            $user->countryid = Country::getCountryId( $country );
            try {
                $user->save();
                $id = $user->id;
            }
            catch( ModelValidationException $e ) {
                go( 'user', 'create', array( $e->error => true ) );
            }
            $_SESSION[ 'user' ] = array(
                'id' => $id,
                'username' => $username
            );
            go();
        }

        public static function view( $username, $notvalid ) {
            if ( $username === NULL ) {
                throw new HTTPNotFoundException();
            }
            include 'models/user.php';
            include 'models/extentions.php';
            include 'models/image.php';
            include 'models/country.php';
            try { 
                $user = User::find_by_username( $username );
            }
            catch ( ModelNotFoundException $e ) {
                throw new HTTPNotFoundException();
            }
            $country = Country::getCountryName( $user->countryid );
            $config = getConfig();
            $image = new Image( $username );
            $avatarname = $image->getCurrentImage();
            $target_path = $config[ 'paths' ][ 'avatar_path' ] . $avatarname;
            include 'views/user/view.php';
        }

        public static function update( $password, $password_new, $password_repeat, $country, $email ) {
            include 'models/user.php';
            include 'models/country.php';
            if ( !isset( $_SESSION[ 'user' ] ) ) {
                throw new HTTPUnauthorizedException();
            }
            $user = new User( $_SESSION[ 'user' ][ 'id' ] );
            if ( $user->authenticatesWithPassword( $password ) ) {
                if ( !empty( $password_new ) ) {
                    if ( $password_new !== $password_repeat ) {
                        go( 'user', 'update', array( 'not_matched' => true ) );
                    }
                    $user->password = $password_new;
                }
                else {
                    $user->password = $password;
                }
                if ( !empty( $email ) ) {
                    $user->email = $email;
                }
                if ( $country !== 'Select Country' ) {
                    $user->countryid = Country::getCountryId( $country );
                }
                try { 
                    $user->save();
                }
                catch ( ModelValidationException $e ) {
                    go( 'user', 'update', array( $e->error => true ) );
                }
                go();
            }
            go( 'user', 'update', array( 'wrong_pass' => true ) );
        }

        public static function delete() {
            include 'models/user.php';
            if ( !isset( $_SESSION[ 'user' ] ) ) {
                throw new HTTPUnauthorizedException();
            }
            $user = new User( $_SESSION[ 'user' ][ 'id' ] );
            $user->delete();
            unset( $_SESSION[ 'user' ] );
            go();
        }

        public static function createView( $empty_user, $empty_mail, $empty_pass, $empty_pass_repeat, 
                $not_matched, $user_used, $small_pass, $mail_used, $mail_notvalid, $empty_country, $not_accepted ) {
            include 'views/user/create.php';
        }

        public static function updateView( $small_pass, $not_matched, $wrong_pass, $mail_notvalid, $mail_used, $empty_country  ) {
            include 'views/user/update.php';
        }
    }
?>
