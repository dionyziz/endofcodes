<?php
    Migration::alterField( 'users', 'forgotpasswordexptime', 'forgotpasswordrequestcreated', 'DATETIME NULL DEFAULT NULL' );
?>
