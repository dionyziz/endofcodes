<?php
    require_once 'migrate.php';

    Migration::addField( 'users', 'dob', 'date NOT NULL' );
?>
