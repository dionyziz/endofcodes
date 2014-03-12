<?php
    class UserController extends ControllerBase {
        public function create( $username = '', $password = '', $password_repeat = '', $email = '',
                                $countryid = '', $day = '', $month = '', $year = '' ) {
            require_once 'models/country.php';
            if ( $password !== $password_repeat ) {
                go( 'user', 'create', [ 'password_not_matched' => true ] );
            }
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
            $user->country = $country;
            $user->dateOfBirth = compact( 'day', 'month', 'year' );
            try {
                $user->save();
            }
            catch( ModelValidationException $e ) {
                go( 'user', 'create', [ $e->error => true ] );
            }
            $_SESSION[ 'user' ] = $user;
            go();
        }

        public function view( $username ) {
            if ( $username === NULL ) {
                throw new HTTPNotFoundException();
            }
            require_once 'models/extentions.php';
            require_once 'models/image.php';
            require_once 'models/country.php';
            require_once 'models/follow.php';
            try {
                $user = User::findByUsername( $username );
            }
            catch ( ModelNotFoundException $e ) {
                throw new HTTPNotFoundException();
            }
            if ( isset( $_SESSION[ 'user' ] ) ) {
                try {
                    $follow = new Follow( $_SESSION[ 'user' ]->id, $user->id );
                    $followExists = true;
                }
                catch ( ModelNotFoundException $e ) {
                    $followExists = false;
                }
            }
            require_once 'views/user/view.php';
        }

        public function update( $password = '', $password_new = '', $password_repeat = '',
                $countryid = '', $email = '' ) {
            require_once 'models/country.php';
            if ( !isset( $_SESSION[ 'user' ] ) ) {
                throw new HTTPUnauthorizedException();
            }
            $user = $_SESSION[ 'user' ];
            if ( !empty( $password_new ) || !empty( $password_repeat ) ) {
                if ( $user->authenticatesWithPassword( $password ) ) {
                    if ( $password_new !== $password_repeat ) {
                        go( 'user', 'update', [ 'password_new_not_matched' => true ] );
                    }
                    $user->password = $password_new;
                }
                else {
                    go( 'user', 'update', [ 'password_wrong' => true ] );
                }
            }
            $user->email = $email;
            try {
                $user->country = new Country( $countryid );
            }
            catch ( ModelNotFoundException $e ) {
            }
            try {
                $user->save();
            }
            catch ( ModelValidationException $e ) {
                go( 'user', 'update', [ $e->error => true ] );
            }
            go();
        }

        public function delete() {
            if ( !isset( $_SESSION[ 'user' ] ) ) {
                throw new HTTPUnauthorizedException();
            }
            $user = $_SESSION[ 'user' ];
            $user->delete();
            unset( $_SESSION[ 'user' ] );
            go();
        }

        public function createView( $username_empty, $username_invalid, $username_used, $email_empty, $email_used, $email_invalid,
                $password_empty, $password_not_matched, $password_small ) {
            require_once 'models/geolocation.php';
            require_once 'models/country.php';
            $countries = Country::findAll();
            $location = Location::getCountryName();
            require 'views/user/create.php';
        }

        public function updateView( $image_invalid, $password_new_small, $password_new_not_matched, $password_wrong,
                $email_invalid, $email_used ) {
            require_once 'models/country.php';
            if ( !isset( $_SESSION[ 'user' ] ) ) {
                throw new HTTPUnauthorizedException();
            }
            $user = $_SESSION[ 'user' ];
            $countries = Country::findAll();
            require 'views/user/update.php';
        }
    }
?>
