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
                LIMIT 1;', array( "username" => $username ) );
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
                LIMIT 1;', array( "mail" => $mail ) );
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
            $array = encrypt( $password );
            db(
                'INSERT INTO
                    users
                SET
                    username = :username,
                    password = :password,
                    email = :email,
                    salt = :salt;',
                array( "username" => $username, "password" => $array[ 'hash' ], "email" => $email, "salt" => $array[ 'salt' ] ) 
            );
        }

        public function deleteUser( $username ) {
            db(
                'DELETE FROM
                    users
                WHERE
                    username = :username;', array( "username" => $username )
            );
        }

        public function updatePassword( $username, $password ) {
            $array = encrypt( $password );
            db(
                'UPDATE
                    users
                SET
                    password = :password,
                    salt = :salt
                WHERE
                    username = :username',
                array( "username" => $username, "password" => $array[ 'hash' ], "salt" => $array[ 'salt' ] )
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
                LIMIT 1;', array( "username" => $username ) );
            if ( mysql_num_rows( $res ) == 1 ) {
                $row = mysql_fetch_array( $res );
                if ( $row[ 'password' ] == hashing( $password, $row[ 'salt' ] ) ) {
                    return $row[ 'userid' ];
                }
            }
            return false;
        }

        public function getCredentials( $username ) {
            $res = db(
                'SELECT
                    username, userid, password, salt, email
                FROM
                    users
                WHERE
                    username = :username
                LIMIT 1;', array( "username" => $username )
            );
            if ( mysql_num_rows( $res ) == 1 ) {
                $row = mysql_fetch_array( $res );
                return $row;
            }
            else {
                return NULL;
            }
        }
    }
?>
