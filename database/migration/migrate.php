<?php
    function migrate( $sql ) {
        include_once '../../config/config-local.php'; 
        include_once '../../models/database.php';

        $res = mysql_query( $sql );
            
        if ( $res === false ) {
            die( 'Migration failed. SQL query died with the following error: ' . mysql_error() );
        }
        echo 'Migration successful.';
    }
?>
