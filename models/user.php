<?php
    include 'encrypt.php';
    class User extends ActiveRecordBase {
        public $id;
        public $username;
        public $password;
        public $email;
        public $countryid;
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
                $user_info = db_select( 'users', array( 'username', 'email', 'password', 'countryid' ), compact( "id" ) );
                $this->username = $user_info[ 0 ][ 'username' ];
                $this->email = $user_info[ 0 ][ 'email' ];
                $this->password = $user_info[ 0 ][ 'password' ];
                $this->countryid = $user_info[ 0 ][ 'countryid' ];
                $this->id = $id;

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
            $password = $this->password;
            $array = encrypt( $password );
            $password = $array[ 'hash' ];
            $salt = $array[ 'salt' ];
            $email = $this->email;
            $countryid = $this->countryid;
            $res = db_update(
                'users',
                compact( "email", "password", "salt", "countryid" ),
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