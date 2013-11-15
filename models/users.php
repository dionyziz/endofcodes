<?php
    include 'encrypt.php';
    class User {
        public function exists( $username ) {
            $res = db(
                'SELECT
                    username
                FROM
                    users
                WHERE
                    username = :username
                LIMIT 1;', 
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
            $res = db(
                'INSERT INTO
                    users
                SET
                    username = :username,
                    password = :password,
                    email = :email,
                    salt = :salt;',
                compact( "username", "password", "email", "salt" )
            );
            if ( $res === false ) {
                throw new ModelValidationException( 'mail_used' );
            }
            return mysql_insert_id();
        }

        public function delete( $username ) {
            db(
                'DELETE FROM
                    users
                WHERE
                    username = :username
                LIMIT 1;', 
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
            db(
                'UPDATE
                    users
                SET
                    password = :password,
                    salt = :salt
                WHERE
                    username = :username
                LIMIT 1;',
                compact( "username", "password", "salt" )
            );
        }

        public function authenticateUser( $username, $password ) {
            $res = db(
                'SELECT
                    userid, password, salt
                FROM
                    users
                WHERE
                    username = :username
                LIMIT 1;', 
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
            $res = db(
                'SELECT
                    username, userid, password, salt, email
                FROM
                    users
                WHERE
                    username = :username
                LIMIT 1;', 
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
