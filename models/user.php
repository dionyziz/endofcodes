<?php
    include_once 'encrypt.php';

    class User extends ActiveRecordBase {
        public $id;
        public $username;
        public $password;
        public $email;
        public $countryid;
        public $avatarid;
        public $salt;
        protected $exists;
        protected $tableName = 'users';

        public static function find_by_username( $username ) {
            $user = db_select_one( 'users', array( 'id' ), compact( "username" ) );
            if ( empty( $user ) ) {
                throw new ModelNotFoundException();
            }
            return new User( $user[ 'id' ] );
        }

        public function __construct( $id = false ) {
            if ( $id === false ) {
                // new active record object
                $this->exists = false;
            }
            else {
                // existing active record object
                $user_info = db_select_one( 'users', array( 'username', 'email', 'countryid', 'avatarid' ), compact( "id" ) );
                $this->username = $user_info[ 'username' ];
                $this->email = $user_info[ 'email' ];
                $this->countryid = $user_info[ 'countryid' ];
                $this->avatarid = $user_info[ 'avatarid' ];
                $this->id = $id;

                $this->exists = true;
            }
        }

        protected function validate() {
            if ( isset( $this->password ) && strlen( $this->password ) <= 6 ) {
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
            $countryid = $this->countryid;
            $array = encrypt( $password );
            $password = $array[ 'hash' ];
            $salt = $array[ 'salt' ];
            $res = db_insert( 
                'users', 
                compact( "username", "password", "email", "salt", "countryid" )
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
            $countryid = $this->countryid;
            $avatarid = $this->avatarid;
            $res = db_update(
                'users',
                compact( "email", "password", "salt", "countryid", "avatarid" ),
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
