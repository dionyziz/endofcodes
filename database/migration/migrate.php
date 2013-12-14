<?php
    function migrate( $sql_array = array() ) {
        include_once '../../config/config-local.php'; 
        include_once '../../models/database.php';
        include_once '../../models/db.php';

        foreach ( $sql_array as $sql ) {
            $res = db( $sql );
            if ( $res === false ) {
                die( "Migration failed. SQL query died with the following error: " . mysql_error() . "\n" );
            }
        }
        echo "Migration successful.\n";
    }
?>
