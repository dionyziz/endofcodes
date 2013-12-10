<?php
    include_once 'models/encrypt.php';
    include_once 'models/country.php';
    include_once 'models/image.php';

    class User extends ActiveRecordBase {
        public $id;
        public $username;
        public $password;
        public $email;
        public $dob;
        public $country;
        public $image;
        public $salt;
        protected $tableName = 'users';

        public static function find_by_username( $username ) {
            $user = db_select_one( 'users', array( 'id' ), compact( "username" ) );
            if ( empty( $user ) ) {
                throw new ModelNotFoundException();
            }
            return new User( $user[ 'id' ] );
        }

        public function __construct( $id = false ) {
            if ( $id ) {
                // existing active record object
                $user_info = db_select_one( 'users', array( 'dob', 'username', 'email', 'countryid', 'avatarid' ), compact( "id" ) );
                $this->username = $user_info[ 'username' ];
                $this->email = $user_info[ 'email' ];
                $this->country = new Country( $user_info[ 'countryid' ] );
                $this->image = new Image( $user_info[ 'avatarid' ] );
                $this->id = $this->image->id = $id;
                $this->dob = $user_info[ 'dob' ];
                $this->exists = true;
            }
        }

        protected function validate() {
            $password_length = 6;
            if ( empty( $this->username ) ) {
                throw new ModelValidationException( 'empty_user' );
            }
            if ( strpos( $this->username, ' ' ) || preg_match('#[^a-zA-Z0-9]#', $this->username ) ) {
                throw new ModelValidationException( 'invalid_username' );
            }
            if ( empty( $this->password ) && !$this->exists ) {
                throw new ModelValidationException( 'empty_pass' );
            }
            if ( empty( $this->email ) ) {
                throw new ModelValidationException( 'empty_mail' );
            }
            if ( isset( $this->password ) && strlen( $this->password ) <= $password_length ) {
                throw new ModelValidationException( 'small_pass' );
            }
            if ( !filter_var( $this->email, FILTER_VALIDATE_EMAIL ) ) {
                throw new ModelValidationException( 'mail_notvalid' );
            }
        }

        protected function create() {
            // when a user is created he doesn't have an image, so avatarid is by default 0 
            $username = $this->username;
            $password = $this->password;
            $email = $this->email;
            $dob = $this->dob;
            $countryid = $this->country->id;
            $array = encrypt( $password );
            $password = $array[ 'hash' ];
            $salt = $array[ 'salt' ];
            $res = db_insert( 
                'users', 
                compact( "username", "password", "email", "salt", "countryid", "dob" )
            );
            if ( $res === false ) { 
                try {
                    $other_user = User::find_by_username( $username );
                    throw new ModelValidationException( 'user_used' );
                }
                catch ( ModelNotFoundException $e ) {
                    throw new ModelValidationException( 'mail_used' );
                }
            }
            $this->exists = true;
            $this->id = mysql_insert_id();
        }

        protected function update() {
            $id = $this->id;
            if ( isset( $this->password ) ) {
                $array = encrypt( $this->password );
                $this->password = $array[ 'hash' ];
                $this->salt = $array[ 'salt' ];
            }
            $email = $this->email;
            $dob = $this->dob;
            $countryid = $this->country->id;
            $avatarid = $this->image->id;
            $res = db_update(
                'users',
                compact( "email", "password", "salt", "countryid", "avatarid", "dob" ),
                compact( "id" )
            );
            if ( $res === -1 ) {
                throw new ModelValidationException( 'mail_used' );
            }
        }

        public function authenticatesWithPassword( $password ) {
            $username = $this->username;
            $row = db_select(
                'users',
                array( 'id', 'password', 'salt' ),
                compact( "username" )
            );
            if ( !empty( $row ) ) {
                if ( $row[ 0 ][ 'password' ] == hashing( $password, $row[ 0 ][ 'salt' ] ) ) {
                    return true;
                }
            }
            return false;
        }
    }
?>
