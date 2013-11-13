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

        public function mailExists( $mail ) {
            $res = db(
                'SELECT
                    username
                FROM
                    users
                WHERE
                    email = :mail
                LIMIT 1;', 
                compact( "mail" ) 
            );
            if ( mysql_num_rows( $res ) == 1 ) {
                return true;
            }
            return false;
        }

        public function validMail( $mail ) {
            $posat = strrpos( $mail, "@" );
            $posdot = strrpos( $mail, "." );
            if ( $posat < 1 || $posat === false || $posdot === strlen( $mail ) || $posdot === false ) {
                return false;
            }
            return true;
        }

        public function createUser( $username, $password, $email ) {
            if ( strlen( $password ) <= 6 ) {
                throw new RedirectException( 'index.php?resource=user&method=create&small_pass=yes' );
            }
            if ( !User::validMail( $email ) ) {
                throw new RedirectException( 'index.php?mail_notvalid=yes&resource=user&method=create' );
            }
            $array = encrypt( $password );
            $password = $array[ 'hash' ];
            $salt = $array[ 'salt' ];
            db(
                'INSERT INTO
                    users
                SET
                    username = :username,
                    password = :password,
                    email = :email,
                    salt = :salt;',
                compact( "username", "password", "email", "salt" )
            );
        }

        public function delete( $username ) {
            db(
                'DELETE FROM
                    users
                WHERE
                    username = :username;', 
                compact( "username" )
            );
        }

        public function update( $username, $password ) {
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
                    username = :username',
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
            else {
                return false;
            }
        }
    }
?>
