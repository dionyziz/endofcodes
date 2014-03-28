<?php
    function throwDBException( $message ) {
        throw new DBException( $message );
    }
    function dbInit() {
        global $config;

        mysql_connect( $config[ 'db' ][ 'host' ], $config[ 'db' ][ 'user' ], $config[ 'db' ][ 'pass' ] ) or throwDBException( 'Failed to connect to MySQL: ' . mysql_error() );

        mysql_select_db( $config[ 'db' ][ 'dbname' ] ) or throwDBException( 'Failed to select MySQL database: ' . mysql_error() );
    }
?>
