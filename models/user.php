<?php
    include_once 'models/encrypt.php';
    include_once 'models/country.php';
    include_once 'models/image.php';

    class User extends ActiveRecordBase {
        protected $attributes = [ 'username', 'password', 'dob', 'salt', 'boturl', 'countryid', 'avatarid', 'email', 'sessionid', 'forgotpasswordtoken', 'forgotpasswordrequestcreated' ];
        public $username;
        public $password;
        public $email;
        public $country;
        public $image;
        public $salt;
        public $dateOfBirth;
        public $boturl;
        protected $dob;
        protected $tableName = 'users';

        public static function findByUsername( $username ) {
            try {
                $user = dbSelectOne( 'users', [ 'id' ], compact( "username" ) );
            }
            catch ( DBException $e ) {
                throw new ModelNotFoundException();
            }
            return new User( $user[ 'id' ] );
        }

        public static function findBySessionId( $sessionid ) {
            if ( empty( $sessionid ) ) {
                throw new ModelNotFoundException();
            }
            try {
                $row = dbSelectOne(
                    'users',
                    [ 'id' ],
                    compact( "sessionid" )
                );
            }
            catch ( DBException $e ) {
                throw new ModelNotFoundException();
            }
            return new User( $row[ 'id' ] );
        }

        public static function passwordValidate( $password ) {
            global $config;

            if ( empty( $password ) ) {
                throw new ModelValidationException( 'password_empty' );
            }
            if ( strlen( $password ) < $config[ 'pass_min_len' ] ) {
                throw new ModelValidationException( 'password_invalid' );
            }
        }

        public function __construct( $id = false ) {
            if ( $id ) {
                // existing active record object
                try {
                    $user_info = dbSelectOne( 'users', [ 'dob', 'username', 'email', 'countryid', 'avatarid', 'forgotpasswordrequestcreated', 'forgotpasswordtoken' ], compact( "id" ) );
                }
                catch ( DBException $e ) {
                    throw new ModelNotFoundException();
                }
                $this->username = $user_info[ 'username' ];
                $this->email = $user_info[ 'email' ];
                $this->country = new Country( $user_info[ 'countryid' ] );
                $this->image = new Image( $user_info[ 'avatarid' ] );
                $this->id = $id;
                $this->dob = $user_info[ 'dob' ];
                $this->forgotPasswordToken = $user_info[ 'forgotpasswordtoken' ];
                $this->forgotPasswordRequestCreated = $user_info[ 'forgotpasswordrequestcreated' ];
                $this->exists = true;
            }
        }
        
        public static function findByEmail( $email ) {
            try {
                $user = dbSelectOne( 'users', [ 'id' ], compact( "email" ) );
            }
            catch ( DBException $e ) {
                throw new ModelNotFoundException();
            }
            return new User( $user[ 'id' ] );
        }

        protected function onBeforeSave() {
            global $config;

            if ( empty( $this->username ) ) {
                throw new ModelValidationException( 'username_empty' );
            }
            if ( preg_match( '#[^a-zA-Z0-9._]#', $this->username ) ) {
                throw new ModelValidationException( 'username_invalid' );
            }
            if ( empty( $this->password ) && !$this->exists ) {
                throw new ModelValidationException( 'password_empty' );
            }
            if ( !$this->exists && empty( $this->email ) ) {
                throw new ModelValidationException( 'email_empty' );
            }
            if ( isset( $this->password ) && strlen( $this->password ) < $config[ 'pass_min_len' ] ) {
                if ( $this->exists ) {
                    throw new ModelValidationException( 'password_new_small' );
                }
                throw new ModelValidationException( 'password_small' );
            }
            if ( !filter_var( $this->email, FILTER_VALIDATE_EMAIL ) ) {
                throw new ModelValidationException( 'email_invalid' );
            }

            if ( isset( $this->password ) ) {
                $array = encrypt( $this->password );
                $this->password = $array[ 'hash' ];
                $this->salt = base64_encode( $array[ 'salt' ] );
            }

            if ( isset( $this->country ) ) {
                $this->countryid = $this->country->id;
            }
            else {
                $this->countryid = 0;
            }
            if ( isset( $this->image ) ) {
                $this->imageid = $this->image->id;
            }
            else {
                $this->imageid = 0;
            }
        }

        protected function onBeforeCreate() {
            $day = intval( $this->dateOfBirth[ 'day' ] );
            $month = intval( $this->dateOfBirth[ 'month' ] );
            $year = intval( $this->dateOfBirth[ 'year' ] );
            if ( !checkdate( $day, $month, $year ) ) {
                $day = $month = $year = 0;
            }
            $dob = $this->dob = $year . '-' . $month . '-' . $day;
            $this->avatarid = 0;
            $this->generateSessionId();
        }

        protected function onSave() {
            unset( $this->password );
            unset( $this->salt );
        }

        protected function onCreateError( $e ) {
            try {
                User::findByUsername( $this->username );
                throw new ModelValidationException( 'username_used' );
            }
            catch ( ModelNotFoundException $e ) {
                try { 
                    User::findByEmail( $this->email ); 
                    throw new ModelValidationException( 'email_used' );
                } 
                catch ( ModelNotFoundException $e ) {
                    throw $e;
                }
            }
        }

        protected function update() {
            $id = $this->id;
            $email = $this->email;
            $dob = $this->dob;
            $forgotpasswordtoken = $this->forgotPasswordToken;
            $forgotpasswordrequestcreated = $this->forgotPasswordRequestCreated;
            $sessionid = $this->sessionid;
            $countryid = $this->countryid;
            $avatarid = $this->imageid;
            if ( isset( $this->password ) ) {
                $password = $this->password;
            }
            if ( isset( $this->salt ) ) {
                $salt = $this->salt;
            }

            try {
                $res = dbUpdate(
                    'users',
                    compact( "email", "password", "salt", "countryid", "avatarid", "dob", "sessionid", "forgotpasswordrequest", "forgotpasswordtoken" ),
                    compact( "id" )
                );
            }
            catch ( DBException $e ) {
                throw new ModelValidationException( 'email_used' );
            }
        }

        protected function generateSessionId() {
            $value = openssl_random_pseudo_bytes( 32 );
            $sessionid = base64_encode( $value );
            $this->sessionid = $sessionid;
        }

        public function authenticatesWithPassword( $password ) {
            $username = $this->username;
            $row = dbSelectOne(
                'users',
                [ 'id', 'password', 'salt' ],
                compact( "username" )
            );
            if ( !empty( $row ) ) {
                if ( $row[ 'password' ] == hashing( $password, base64_decode( $row[ 'salt' ] ) ) ) {
                    return true;
                }
            }
            return false;
        }

        public function renewSessionId() {
            $this->generateSessionId();
            $this->save();
        }

        public function createForgotPasswordLink() {
            global $config;

            $value = md5( 32 );
            $this->forgotPasswordToken = $value;
            $this->forgotPasswordRequestCreated = date("Y-m-d h:i:s");
            $this->save();
            $email = $this->email;
            $username = urlencode( $this->username );
            $link = $config[ 'base'] . "/forgotpasswordrequest/view?username=$username&token=$value";
            $mailVars = [ 
                'username' => $username,
                'link' => $link
            ];
            $this->mailFromExternalView( $email, "views/forgotpasswordmail.php", 'Password Reset', $mailVars );
            return $link;
        }
        
        public function mailFromExternalView( $email, $extView, $subject = '', $vars = [] ) {
            if ( !file_exists( $extView ) ) {
                throw new ModelNotFoundException();
            }
            extract( $vars );
            $data = file_get_contents( $extView );
            mail( $email, $subject, $data );
        }

        public function revokePasswordCheck( $token ) {
            global $config;

            if ( $token == $this->forgotPasswordToken ) {
                if ( empty( $token ) ) {
                    throw new HTTPUnauthorizedException();
                }
                $datetime = strtotime( $this->forgotPasswordRequestCreated );
                $now = strtotime( date( "Y-m-d h:i:s" ) );
                $period = $now - $datetime;
                if ( $period > $config[ 'forgot_password_exp_time' ] ) {
                    throw new ModelValidationException( 'link_expired' );
                } 
                return;
            }
            throw new HTTPUnauthorizedException();
        }

        public function toJson() {
            return json_encode( $this->jsonSerialize() );
        }

        public function jsonSerialize() {
            $username = $this->username;
            $userid = $this->id;
            return compact( 'username', 'userid' );
        }
    }
?>
