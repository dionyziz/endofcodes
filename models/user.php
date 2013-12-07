<?php
    include_once 'encrypt.php';
    class User extends ActiveRecordBase {
        public $id;
        public $username;
        public $password;
        public $email;
        public $dob;
        public $countryid;
        public $avatarid;
        public $salt;
        public $changedPass;
        protected $exists;
        protected $tableName = 'users';

        public static function find_by_username( $username ) {
            $users = db_select( 'users', array( 'id' ), compact( "username" ) );
            if ( empty( $users ) ) {
                throw new ModelNotFoundException();
            }
            return new User( $users[ 0 ][ 'id' ] );
        }

        public function __construct( $id = false ) {
            if ( $id === false ) {
                // new active record object
                $this->exists = false;
            }
            else {
                // existing active record object
                $user_info = db_select_one( 'users', array( 'salt', 'username', 'email', 'password', 'countryid', 'avatarid' ), compact( "id" ) );
                $this->username = $user_info[ 'username' ];
                $this->email = $user_info[ 'email' ];
                $this->password = $user_info[ 'password' ];
                $this->countryid = $user_info[ 'countryid' ];
                $this->avatarid = $user_info[ 'avatarid' ];
                $this->salt = $user_info[ 'salt' ];
                $this->id = $id;
                $this->dob[ 'day' ] = $user_info[ 'day' ];
                $this->dob[ 'month' ] = $user_info[ 'month' ];
                $this->dob[ 'year'] = $user_info[ 'year' ];
                $this->exists = true;
            }
        }

        protected function validate() {
            if ( strlen( $this->password ) <= 6 ) {
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
            $day = $this->dob[ 'day' ];
            $month = $this->dob[ 'month' ];
            $year = $this->dob[ 'year' ];
            $countryid = $this->countryid;
            $array = encrypt( $password );
            $password = $array[ 'hash' ];
            $salt = $array[ 'salt' ];
            $res = db_insert( 
                'users', 
                compact( "username", "password", "email", "salt", "countryid", "day", "month", "year" )
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
            if ( $this->changedPass ) {
                $array = encrypt( $this->password );
                $this->password = $array[ 'hash' ];
                $this->salt = $array[ 'salt' ];
            }
            $salt = $this->salt;
            $password = $this->password;
            $email = $this->email;
            $day = $this->dob[ 'day' ];
            $month = $this->dob[ 'month' ];
            $year = $this->dob[ 'year' ];
            $countryid = $this->countryid;
            $avatarid = $this->avatarid;
            $res = db_update(
                'users',
                compact( "email", "password", "salt", "countryid", "avatarid", "day", "month", "year" ),
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
