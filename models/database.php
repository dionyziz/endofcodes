<?php
    include 'config/configdb.php';
    $cred = getCred();
    mysql_connect( $cred[ 'host' ], $cred[ 'user' ], $cred[ 'password' ] );
    mysql_select_db( "endofcodes" );
?>
