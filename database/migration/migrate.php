<?php
    function migrate( $sql ) {
        include '../../config/config-local.php'; 
        include '../../models/database.php';

        $res = mysql_query( $sql );
            
        if ( $res === false ) {
            die( 'Migration failed. SQL query died with the following error: ' . mysql_error() );
        }
        echo 'Migration successful.';
    }
?>
