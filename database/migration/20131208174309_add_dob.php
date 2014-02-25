<?php
    require 'migrate.php';

    Migration::addField( 'users', 'dob', 'NOT NULL' );
?>
