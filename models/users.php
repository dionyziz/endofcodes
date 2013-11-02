<?php
    include 'query.php';
    include 'encrypt.php';
    function userExists( $username ) {
        $res = prep_query(
            'SELECT
                username
            FROM
                users
            WHERE
                username = ?
            LIMIT 1;', array( $username ) );
        if ( mysql_num_rows( $res ) == 1 ) {
            return true;
        }
        return false;
    }

    function mailExists( $mail ) {
        $res = prep_query(
            'SELECT
                username
            FROM
                users
            WHERE
                email = ?
            LIMIT 1;', array( $mail ) );
        if ( mysql_num_rows( $res ) == 1 ) {
            return true;
        }
        return false;
    }

    function createUser( $username, $password, $email ) {
        $array = encrypt( $password );
        prep_query(
            'INSERT INTO
                users
            SET
                username = ?,
                password = ?,
                email = ?,
                salt = ?;', array( $username, $array[ 'password' ], $email, $array[ 'salt' ] ) );
        die( $array[ 'salt' ] );
    }

    function athenticateUser( $username, $password ) {
        $res = prep_query(
            'SELECT
                userid, password, salt
            FROM
                users
            WHERE
                username = ?
            LIMIT 1;', array( $username ) );
        if ( mysql_num_rows( $res ) == 1 ) {
            $row = mysql_fetch_array( $res );
            if ( $row[ 'password' ] == hash( 'sha256', $password . $row[ 'salt' ] ) ) {
                return $row[ 'userid' ];
            }
        }
        return false;
    }
?>
