<?php
    include 'migrate.php';

    Migration::addField( 'users', 'dob', 'NOT NULL' );
?>
