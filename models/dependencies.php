<?php
    include 'models/db.php';
    if ( file_exists( 'config/config-local.php' ) ) {
        include 'config/config-local.php';
    }
    else {
        include 'config/config.php';
    }
    include 'models/database.php';
    include 'models/base.php';
    session_start();
    error_reporting( E_ALL );
    ini_set( 'display_errors', '1' );
?>
