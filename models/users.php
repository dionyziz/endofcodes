<?php
    include 'db.php';
    include 'encrypt.php';
    function userExists( $username ) {
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

    function mailExists( $mail ) {
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

    function createUser( $username, $password, $email ) {
        $array = encrypt( $password );
        db(
            'INSERT INTO
                users
            SET
                username = :username,
                password = :password,
                email = :email,
                salt = :salt;', array( "username" => $username, "password" => $array[ 'hash' ], "email" => $email, "salt" => $array[ 'salt' ] ) );
    }

    function authenticateUser( $username, $password ) {
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
?>
