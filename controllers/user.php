<?php
    class UserController {
        public static function create( $username = '', $password = '', $password_repeat = '', $email = '', 
                $countryname = '', /*$accept = false, */$day = '', $month = '', $year = '' ) {
            /*if ( $accept === false ) {
                go( 'user', 'create', array( 'not_accepted' => true ) );
            }
            if ( !array_search( $month, $months ) ) {
                go( 'user', 'create', array( 'empty_month' => true ) );
            }*/
            include_once 'models/user.php';
            include_once 'models/country.php';

            if ( $password !== $password_repeat ) {
                go( 'user', 'create', array( 'pass_not_matched' => true ) );
            }
            $day = intval( $day );
            $month = intval( $month );
            $year = intval( $year );
            if ( !checkdate( $day, $month, $year ) ) {
                $day = $month = $year = 0;
            }
            $dob = $year . '-' . $month . '-' . $day; 
            $country = new Country();
            try {
                $country = Country::findByName( $countryname );
            }
            catch ( ModelNotFoundException $e ) {
                $country->name = '';
                $country->id = 0;
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

        public static function update( $password = '', $password_new = '', $password_repeat = '', $countryname = '', $email = '' ) {
            include_once 'models/user.php';
            include_once 'models/country.php';
            if ( !isset( $_SESSION[ 'user' ] ) ) {
                throw new HTTPUnauthorizedException();
            }
            $user = new User( $_SESSION[ 'user' ][ 'id' ] );
            if ( !empty( $password_new ) || !empty( $password_repeat ) ) {
                if ( $user->authenticatesWithPassword( $password ) ) {
                    if ( $password_new !== $password_repeat ) {
                        go( 'user', 'update', array( 'new_pass_not_matched' => true ) );
                    }
                    $user->password = $password_new;
                }
                else {
                    go( 'user', 'update', array( 'old_pass_wrong' => true ) );
                }
            }
            if ( !empty( $email ) ) {
                $user->email = $email;
            }
            try {
                $user->country = Country::findByName( $countryname );
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

        public static function createView( $username_empty, $username_invalid, $mail_empty, $pass_empty, $pass_not_matched,
                $user_used, $pass_small, $mail_used, $mail_invalid/*, $country_empty, $terms_not_accepted, $day_empty, $month_empty, $year_empty*/ ) {
            include_once 'models/country.php'; 
            $countries = Country::findAll();
            include 'views/user/create.php';
        }

        public static function updateView( $image_invalid, $pass_small, $new_pass_not_matched, $old_pass_wrong, $mail_invalid, $mail_used, $country_empty ) {
            include_once 'models/country.php';
            $countries = Country::findAll();
            include 'views/user/update.php';
        }
    }
?>
