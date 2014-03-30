<?php
    class UserController extends ControllerBase {
        public function create( $username = '', $password = '', $passwordRepeat = '', $email = '',
                                $countryid = '', $day = '', $month = '', $year = '' ) {
            require_once 'models/country.php';
            if ( $password !== $passwordRepeat ) {
                go( 'user', 'create', [ 'passwordNotMatched' => true ] );
            }
            try {
                $country = new Country( $countryid );
            }
            catch ( ModelNotFoundException $e ) {
                $country = new Country();
            }
            $_SESSION[ 'createPost' ] = compact( 'username', 'email' );
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

        public function update( $password = '', $passwordNew = '', $passwordRepeat = '',
                                $countryid = '', $email = '' ) {
            require_once 'models/country.php';
            if ( !isset( $_SESSION[ 'user' ] ) ) {
                throw new HTTPUnauthorizedException();
            }
            $user = $_SESSION[ 'user' ];
            if ( !empty( $passwordNew ) || !empty( $passwordRepeat ) ) {
                if ( $user->authenticatesWithPassword( $password ) ) {
                    if ( $passwordNew !== $passwordRepeat ) {
                        go( 'user', 'update', [ 'passwordNewNotMatched' => true ] );
                    }
                    $user->password = $passwordNew;
                }
                else {
                    go( 'user', 'update', [ 'passwordWrong' => true ] );
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

        public function createView( $usernameEmpty, $usernameInvalid, $usernameUsed, $emailEmpty, $emailUsed, $emailInvalid,
                                    $passwordEmpty, $passwordNotMatched, $passwordSmall ) {
            require_once 'models/geolocation.php';
            require_once 'models/country.php';
            $countries = Country::findAll();
            try {
                $location = Location::getCountryName( $_SERVER[ 'REMOTE_ADDR' ] );
            }
            catch ( ModelNotFoundException $e ) {
                $location = ''; 
            }
            require 'views/user/create.php';
        }

        public function updateView( $imageInvalid, $passwordNewSmall, $passwordNewNotMatched, $passwordWrong,
                                    $emailInvalid, $emailUsed ) {
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
