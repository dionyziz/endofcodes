<?php
    function dbInit() {
        global $config;

        mysql_connect( $config[ 'db' ][ 'host' ], $config[ 'db' ][ 'user' ], $config[ 'db' ][ 'pass' ] ) or die( 'Failed to connect to MySQL: ' . mysql_error() );

        mysql_select_db( $config[ 'db' ][ 'dbname' ] ) or die( 'Failed to select MySQL database: ' . mysql_error() );
    }
?>
