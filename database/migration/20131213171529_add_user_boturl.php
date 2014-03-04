<?php
    require_once 'migrate.php';

    Migration::addField( 'users', 'boturl', 'VARCHAR(100) NOT NULL' );
?>
