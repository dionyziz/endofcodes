<?php
    include 'config/configdb.php';
    $cred = getCred();
    mysql_connect( $cred[ 'host' ], $cred[ 'user' ], $cred[ 'password' ] );
    $db_name = getDbName();
    mysql_select_db( $db_name );
?>
