<?php
    include 'encrypt.php';
    include 'models/base.php';

    class User extends ActiveRecordBase {
        public $id;
        public $username;
        public $password;
        public $email;
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
                $user_info = db_select( 'users', array( 'username', 'email', 'password' ), compact( "id" ) );
                $this->username = $user_info[ 0 ][ 'username' ];
                $this->email = $user_info[ 0 ][ 'email' ];
                $this->password = $user_info[ 0 ][ 'password' ];
                $this->id = $id;

                $this->exists = true;
            }
        }

        protected function validate() {
            if ( strlen( $this->password ) <= 6 ) {
                throw new ModelValidationException( 'small_pass' );
            }
            if ( !filter_var( $this->email, FILTER_VALIDATE_EMAIL ) {
                throw new ModelValidationException( 'mail_notvalid' );
            }
        }

        protected function create() {
            $username = $this->username;
            $password = $this->password;
            $email = $this->email;
            $array = encrypt( $password );
            $password = $array[ 'hash' ];
            $salt = $array[ 'salt' ];
            $res = db_insert( 
                'users', 
                compact( "username", "password", "email", "salt" )
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
            $username = $this->username;
            db_update(
                'users',
                compact( "email", "username", "password", "salt" ),
                compact( "id" )
            );
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
