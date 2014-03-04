<?php
    require_once 'migrate.php';

    Migration::alterField( 'users', 'forgotpasswordexptime', 'forgotpasswordrequestcreated', 'DATETIME NULL DEFAULT NULL' );
?>
