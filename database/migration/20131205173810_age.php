<?php
    include '../../config/config-local.php';
    include '../../models/database.php';

    $res = mysql_query(
        'ALTER TABLE
            users
        ADD COLUMN
            age int(3) unsigned NOT NULL;'
    );
    if ( $res === false ) {
        die( 'SQL query died with the following error: ' . mysql_error() );
    }
    echo 'good';
?>
