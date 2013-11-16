<?php
    include 'encrypt.php';
    class User {
        public function exists( $username ) {
            $res = db_select( 
                'users', 
                array( 'username' ), 
                compact( "username" ) 
            );
            if ( mysql_num_rows( $res ) == 1 ) {
                return true;
            }
            return false;
        }

        public function create( $username, $password, $email ) {
            if ( strlen( $password ) <= 6 ) {
                throw new ModelValidationException( 'small_pass' );
            }
            if ( !Mail::valid( $email ) ) {
                throw new ModelValidationException( 'mail_notvalid' );
            }
            $array = encrypt( $password );
            $password = $array[ 'hash' ];
            $salt = $array[ 'salt' ];
            $res = db_insert( 
                'users', 
                compact( "username", "password", "email", "salt" )
            );
            if ( $res === false ) {
                // if this query caused an error, then we must have a duplicate username or email
                // check if we have a duplicate username
                // if not, we have a duplicate email
                if ( User::exists( $username ) ) {
                    throw new ModelValidationException( 'user_used' );
                }
                throw new ModelValidationException( 'mail_used' );
            }
            return mysql_insert_id();
        }

        public function delete( $username ) {
            db_delete(
                'users',
                compact( "username" )
            );
        }

        public function update( $username, $password ) {
            if ( strlen( $password ) <= 6 ) {
                throw new RedirectException( 'index.php?resource=user&method=update&small_pass=yes' );
            }
            $array = encrypt( $password );
            $password = $array[ 'hash' ];
            $salt = $array[ 'salt' ];
            db_update(
                'users',
                compact( "password", "salt" ),
                compact( "username" )
            );
        }

        public function authenticateUser( $username, $password ) {
            $res = db_select(
                'users',
                array( 'userid', 'password', 'salt' ),
                compact( "username" )
            );
            if ( mysql_num_rows( $res ) == 1 ) {
                $row = mysql_fetch_array( $res );
                if ( $row[ 'password' ] == hashing( $password, $row[ 'salt' ] ) ) {
                    return $row[ 'userid' ];
                }
            }
            return false;
        }

        public function get( $username ) {
            $res = db_select(
                'users',
                array( 'username', 'userid', 'password', 'salt', 'email' ),
                compact( "username" )
            );
            if ( mysql_num_rows( $res ) == 1 ) {
                $row = mysql_fetch_array( $res );
                return $row;
            }
            return false;
        }
    }
?>
