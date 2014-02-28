<?php
    include_once 'migrate.php';

    Migration::addField( 'users', 'forgotpasswordexptime', 'datetime' );
?>
