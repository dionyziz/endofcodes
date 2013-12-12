<?php
    class UserController {
        public static function create( $username = '', $password = '', $password_repeat = '', $email = '', 
                $countryid = '', $day = '', $month = '', $year = '' ) {
            include_once 'models/user.php';
            include_once 'models/country.php';

            if ( $password !== $password_repeat ) {
                go( 'user', 'create', array( 'password_not_matched' => true ) );
            }
            $day = intval( $day );
            $month = intval( $month );
            $year = intval( $year );
            if ( !checkdate( $day, $month, $year ) ) {
                $day = $month = $year = 0;
            }
            $dob = $year . '-' . $month . '-' . $day; 
            try {
                $country = new Country( $countryid );
            }
            catch ( ModelNotFoundException $e ) {
                $country = new Country();
            }
            $_SESSION[ 'create_post' ] = compact( 'username', 'email' );
            $user = new User();
            $user->username = $username;
            $user->password = $password;
            $user->email = $email;
            $user->dob = $dob;
            $user->country = $country;
            try {
                $user->save();
                $id = $user->id;
            }
            catch( ModelValidationException $e ) {
                go( 'user', 'create', array( $e->error => true ) );
            }
            $_SESSION[ 'user' ] = compact( 'id', 'username' );
            go();
        }

        public static function view( $username ) {
            if ( $username === NULL ) {
                throw new HTTPNotFoundException();
            }
            include_once 'models/user.php';
            include_once 'models/extentions.php';
            include_once 'models/image.php';
            include_once 'models/country.php';
            try { 
                $user = User::findByUsername( $username );
            }
            catch ( ModelNotFoundException $e ) {
                throw new HTTPNotFoundException();
            }
            include_once 'views/user/view.php';
        }

        public static function update( $password = '', $password_new = '', $password_repeat = '', $countryid = '', $email = '' ) {
            include_once 'models/user.php';
            include_once 'models/country.php';
            if ( !isset( $_SESSION[ 'user' ] ) ) {
                throw new HTTPUnauthorizedException();
            }
            $user = new User( $_SESSION[ 'user' ][ 'id' ] );
            if ( !empty( $password_new ) || !empty( $password_repeat ) ) {
                if ( $user->authenticatesWithPassword( $password ) ) {
                    if ( $password_new !== $password_repeat ) {
                        go( 'user', 'update', array( 'password_new_not_matched' => true ) );
                    }
                    $user->password = $password_new;
                }
                else {
                    go( 'user', 'update', array( 'password_wrong' => true ) );
                }
            }
            if ( !empty( $email ) ) {
                $user->email = $email;
            }
            try {
                $user->country = new Country( $countryid );
            }
            catch ( ModelNotFoundException $e ) {
            }
            try { 
                $user->save();
            }
            catch ( ModelValidationException $e ) {
                go( 'user', 'update', array( $e->error => true ) );
            }
            go();
        }

        public static function delete() {
            include_once 'models/user.php';
            if ( !isset( $_SESSION[ 'user' ] ) ) {
                throw new HTTPUnauthorizedException();
            }
            $user = new User( $_SESSION[ 'user' ][ 'id' ] );
            $user->delete();
            unset( $_SESSION[ 'user' ] );
            go();
        }

        public static function createView( $username_empty, $username_invalid, $username_used, $email_empty, $email_used, $email_invalid, 
                $password_empty, $password_not_matched, $password_small ) {
            include_once 'models/country.php'; 
            $countries = Country::findAll();
            include 'views/user/create.php';
        }

        public static function updateView( $image_invalid, $password_new_small, $password_new_not_matched, $password_wrong, $email_invalid, $email_used ) {
            include_once 'models/country.php';
            $countries = Country::findAll();
            include 'views/user/update.php';
        }
    }
?>
