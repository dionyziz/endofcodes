<?php
    include_once 'models/encrypt.php';
    include_once 'models/country.php';
    include_once 'models/image.php';

    class User extends ActiveRecordBase {
        protected $attributes = array( 'username', 'password', 'dob', 'salt', 'boturl', 'countryid', 'avatarid', 'email', 'sessionid', 'forgotPasswordToken' );
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
                $user = dbSelectOne( 'users', array( 'id' ), compact( "username" ) );
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
                    array( 'id' ), 
                    compact( "sessionid" ) 
                );
            }
            catch ( DBException $e ) {
                throw new ModelNotFoundException();
            }
            return new User( $row[ 'id' ] );
        }

        public function __construct( $id = false ) {
            if ( $id ) {
                // existing active record object
                try {
                    $user_info = dbSelectOne( 'users', array( 'dob', 'username', 'email', 'countryid', 'avatarid' ), compact( "id" ) );
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
                $this->exists = true;
            }
        }

        protected function validate() {
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
        }

        protected function onBeforeCreate() {
            $day = intval( $this->dateOfBirth[ 'day' ] ); 
            $month = intval( $this->dateOfBirth[ 'month' ] );
            $year = intval( $this->dateOfBirth[ 'year' ] );
            if ( !checkdate( $day, $month, $year ) ) {
                $day = $month = $year = 0;
            }
            $dob = $this->dob = $year . '-' . $month . '-' . $day; 
            $array = encrypt( $this->password );
            $this->password = $array[ 'hash' ];
            $this->salt = $array[ 'salt' ];
            $this->avatarid = 0;
            $this->generateSessionId();
            if ( isset( $this->country ) ) {
                $this->countryid = $this->country->id;
            }
            else {
                $this->countryid = 0;
            }
        }

        protected function onCreateError() {
            try {
                $other_user = User::findByUsername( $this->username );
                throw new ModelValidationException( 'username_used' );
            }
            catch ( ModelNotFoundException $e ) {
                throw new ModelValidationException( 'email_used' );
            }
        }

        protected function update() {
            $id = $this->id;
            if ( isset( $this->password ) ) {
                $array = encrypt( $this->password );
                $this->password = $password = $array[ 'hash' ];
                $this->salt = $salt = $array[ 'salt' ];
            }
            $email = $this->email;
            $dob = $this->dob;
            $sessionid = $this->sessionid;
            $forgotPasswordToken = $this->forgotPasswordToken;
            if ( isset( $this->country ) ) {
                $countryid = $this->country->id;
            }
            else {
                $countryid = 0;
            }
            if ( isset( $this->image ) ) {
                $avatarid = $this->image->id;
            }
            else {
                $avatarid = 0;
            }
            try {
                $res = dbUpdate(
                    'users',
                    compact( "email", "password", "salt", "countryid", "avatarid", "dob", "sessionid", "forgotPasswordToken" ),
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
                array( 'id', 'password', 'salt' ),
                compact( "username" )
            );
            if ( !empty( $row ) ) {
                if ( $row[ 'password' ] == hashing( $password, $row[ 'salt' ] ) ) {
                    return true;
                }
            }
            return false;
        }

        public function renewSessionId() {
            $this->generateSessionId();
            $this->save();
        }

        public function passwordRevoke() {
            $value = openssl_random_pseudo_bytes( 32 );
            $value = base64_encode( $value );
            $this->generateSessionId();
            $this->forgotPasswordToken = $value;
            $this->save();
            $email = $this->email;
            $link = "localhost/endofcodes/index.php?resource=forgotPasswordRequest&method=create&email=$email&token=$value";
            mail( $this->email, 'testMail', "your random link is $link" );
            return $link;
        }
    }
?>
