<?php
    include_once 'models/db.php';
    if ( file_exists( 'config/config-local.php' ) ) {
        include_once 'config/config-local.php';
    }
    else {
        include_once 'config/config.php';
    }
    include_once 'models/database.php';
    include_once 'models/base.php';
    session_start();
    error_reporting( E_ALL );
    ini_set( 'display_errors', '1' );
?>
