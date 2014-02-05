<?php
    include_once 'models/encrypt.php';
    include_once 'models/country.php';
    include_once 'models/image.php';

    class User extends ActiveRecordBase {
        protected $attributes = array( 'username', 'password', 'dob', 'salt', 'boturl', 'countryid', 'avatarid', 'email', 'sessionid' );
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

        protected function onSave() {
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

        protected function onCreateError( $e ) {
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
            $email = $this->email;
            $dob = $this->dob;
            $sessionid = $this->sessionid;
            $countryid = $this->countryid;
            $avatarid = $this->imageid;
            $password = $this->password;
            $salt = $this->salt;

            try {
                $res = dbUpdate(
                    'users',
                    compact( "email", "password", "salt", "countryid", "avatarid", "dob", "sessionid" ),
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
